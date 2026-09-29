<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Max Mustermann',
            'email' => 'user@example.com',
        ]);

        $household = Household::create([
            'name' => 'Muster-Haushalt',
            'description' => 'Unser gemeinsamer Haushalt',
            'currency' => 'EUR',
            'owner_id' => $admin->id,
        ]);

        $household->members()->attach($admin->id, ['role' => 'owner', 'joined_at' => now()]);
        $household->members()->attach($user2->id, ['role' => 'member', 'joined_at' => now()]);

        // Seed categories
        $seeder = new \App\Http\Controllers\HouseholdController();
        $reflection = new \ReflectionClass($seeder);
        $method = $reflection->getMethod('createDefaultCategories');
        $method->setAccessible(true);
        $method->invoke($seeder, $household);

        $categories = $household->categories()->get();
        $gehalt = $categories->where('name', 'Gehalt')->first();
        $lebensmittel = $categories->where('name', 'Lebensmittel')->first();
        $transport = $categories->where('name', 'Transport')->first();
        $unterhaltung = $categories->where('name', 'Unterhaltung')->first();
        $miete = $categories->where('name', 'Miete')->first();

        // Generate 6 months of sample data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            foreach ([$admin, $user2] as $user) {
                Transaction::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'category_id' => $gehalt?->id,
                    'type' => 'income',
                    'amount' => rand(2500, 4000),
                    'description' => 'Gehalt ' . $date->format('M Y'),
                    'date' => $date->copy()->startOfMonth()->addDays(rand(1, 5)),
                ]);

                Transaction::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'category_id' => $miete?->id,
                    'type' => 'expense',
                    'amount' => 850,
                    'description' => 'Miete',
                    'date' => $date->copy()->startOfMonth()->addDays(2),
                ]);

                Transaction::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'category_id' => $lebensmittel?->id,
                    'type' => 'expense',
                    'amount' => rand(200, 400),
                    'description' => 'Supermarkt',
                    'date' => $date->copy()->startOfMonth()->addDays(rand(5, 25)),
                ]);

                Transaction::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'category_id' => $transport?->id,
                    'type' => 'expense',
                    'amount' => rand(50, 150),
                    'description' => 'ÖPNV / Tanken',
                    'date' => $date->copy()->startOfMonth()->addDays(rand(3, 20)),
                ]);

                Transaction::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'category_id' => $unterhaltung?->id,
                    'type' => 'expense',
                    'amount' => rand(20, 100),
                    'description' => 'Netflix / Kino',
                    'date' => $date->copy()->startOfMonth()->addDays(rand(10, 28)),
                ]);
            }
        }
    }
}
