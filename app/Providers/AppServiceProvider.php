<?php

namespace App\Providers;

use App\Models\Household;
use App\Policies\HouseholdPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Carbon::setLocale('de');
        Gate::policy(Household::class, HouseholdPolicy::class);
    }
}
