<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HouseholdController extends Controller
{
    public function index(): Response
    {
        $households = Auth::user()->households()->with('owner')->withCount('members')->get();
        return Inertia::render('Households/Index', [
            'households' => $households,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Households/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'currency' => 'required|string|size:3',
        ]);

        $household = Household::create([
            ...$validated,
            'owner_id' => Auth::id(),
        ]);

        $household->members()->attach(Auth::id(), [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        // Create default categories
        $this->createDefaultCategories($household);

        return redirect()->route('households.dashboard', $household)
            ->with('success', 'Haushalt erfolgreich erstellt!');
    }

    public function dashboard(Household $household): Response
    {
        $this->authorize('view', $household);

        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Monthly stats
        $monthlyIncome = $household->transactions()
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $monthlyExpenses = $household->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // Last 6 months chart data
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $chartData[] = [
                'month' => $date->translatedFormat('M Y'),
                'income' => (float) $household->transactions()
                    ->where('type', 'income')
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount'),
                'expense' => (float) $household->transactions()
                    ->where('type', 'expense')
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount'),
            ];
        }

        // Category breakdown for current month
        $categoryBreakdown = $household->transactions()
            ->with('category')
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get()
            ->groupBy(fn($t) => $t->category?->name ?? 'Sonstige')
            ->map(fn($group) => [
                'name' => $group->first()->category?->name ?? 'Sonstige',
                'color' => $group->first()->category?->color ?? '#94a3b8',
                'total' => $group->sum('amount'),
            ])->values();

        // Per-member stats
        $memberStats = $household->members()->get()->map(function ($member) use ($household, $currentMonth, $currentYear) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'role' => $member->pivot->role,
                'income' => (float) $household->transactions()
                    ->where('user_id', $member->id)
                    ->where('type', 'income')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->sum('amount'),
                'expenses' => (float) $household->transactions()
                    ->where('user_id', $member->id)
                    ->where('type', 'expense')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->sum('amount'),
            ];
        });

        // Recent transactions
        $recentTransactions = $household->transactions()
            ->with(['user', 'category'])
            ->latest('date')
            ->take(10)
            ->get();

        // Budget overview
        $budgets = $household->budgets()
            ->with('category')
            ->where('year', $currentYear)
            ->where(fn($q) => $q->whereNull('month')->orWhere('month', $currentMonth))
            ->get()
            ->map(function ($budget) use ($household, $currentMonth, $currentYear) {
                $spent = $household->transactions()
                    ->where('type', 'expense')
                    ->where('category_id', $budget->category_id)
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                return [
                    'id' => $budget->id,
                    'name' => $budget->name,
                    'amount' => (float) $budget->amount,
                    'spent' => (float) $spent,
                    'percentage' => $budget->amount > 0 ? min(100, round(($spent / $budget->amount) * 100)) : 0,
                    'color' => $budget->category?->color ?? '#6366f1',
                ];
            });

        return Inertia::render('Households/Dashboard', [
            'household' => $household->load('members'),
            'monthlyIncome' => (float) $monthlyIncome,
            'monthlyExpenses' => (float) $monthlyExpenses,
            'balance' => (float) ($monthlyIncome - $monthlyExpenses),
            'chartData' => $chartData,
            'categoryBreakdown' => $categoryBreakdown,
            'memberStats' => $memberStats,
            'recentTransactions' => $recentTransactions,
            'budgets' => $budgets,
            'currentMonth' => $now->translatedFormat('F Y'),
        ]);
    }

    public function join(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);

        $household = Household::where('invite_code', strtoupper($request->invite_code))->firstOrFail();

        if ($household->isMember(Auth::user())) {
            return back()->with('error', 'Du bist bereits Mitglied dieses Haushalts.');
        }

        $household->members()->attach(Auth::id(), [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('households.dashboard', $household)
            ->with('success', 'Erfolgreich beigetreten!');
    }

    public function regenerateCode(Household $household)
    {
        $this->authorize('update', $household);
        $household->regenerateInviteCode();
        return back()->with('success', 'Einladungscode wurde erneuert.');
    }

    private function createDefaultCategories(Household $household): void
    {
        $categories = [
            ['name' => 'Gehalt', 'color' => '#22c55e', 'icon' => 'briefcase', 'type' => 'income'],
            ['name' => 'Freelance', 'color' => '#10b981', 'icon' => 'laptop', 'type' => 'income'],
            ['name' => 'Miete', 'color' => '#ef4444', 'icon' => 'home', 'type' => 'expense'],
            ['name' => 'Lebensmittel', 'color' => '#f97316', 'icon' => 'shopping-cart', 'type' => 'expense'],
            ['name' => 'Transport', 'color' => '#3b82f6', 'icon' => 'car', 'type' => 'expense'],
            ['name' => 'Unterhaltung', 'color' => '#a855f7', 'icon' => 'tv', 'type' => 'expense'],
            ['name' => 'Gesundheit', 'color' => '#ec4899', 'icon' => 'heart', 'type' => 'expense'],
            ['name' => 'Bildung', 'color' => '#06b6d4', 'icon' => 'book', 'type' => 'expense'],
            ['name' => 'Sonstiges', 'color' => '#94a3b8', 'icon' => 'tag', 'type' => 'both'],
        ];

        foreach ($categories as $cat) {
            $household->categories()->create($cat);
        }
    }
}
