<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Household $household)
    {
        $this->authorize('view', $household);

        $transactions = $household->transactions()
            ->with(['user', 'category'])
            ->latest('date')
            ->paginate(20);

        $categories = $household->categories()->get();

        return Inertia::render('Transactions/Index', [
            'household' => $household,
            'transactions' => $transactions,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Household $household)
    {
        $this->authorize('view', $household);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
            'is_recurring' => 'boolean',
            'recurring_interval' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        $household->transactions()->create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Transaktion erfolgreich hinzugefügt!');
    }

    public function update(Request $request, Household $household, Transaction $transaction)
    {
        $this->authorize('view', $household);
        abort_if($transaction->user_id !== Auth::id() && !in_array($household->getMemberRole(Auth::user()), ['owner', 'admin']), 403);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
        ]);

        $transaction->update($validated);
        return back()->with('success', 'Transaktion aktualisiert!');
    }

    public function destroy(Household $household, Transaction $transaction)
    {
        $this->authorize('view', $household);
        abort_if($transaction->user_id !== Auth::id() && !in_array($household->getMemberRole(Auth::user()), ['owner', 'admin']), 403);

        $transaction->delete();
        return back()->with('success', 'Transaktion gelöscht!');
    }
}
