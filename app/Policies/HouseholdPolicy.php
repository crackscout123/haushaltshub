<?php

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
    public function view(User $user, Household $household): bool
    {
        return $household->isMember($user);
    }

    public function update(User $user, Household $household): bool
    {
        return in_array($household->getMemberRole($user), ['owner', 'admin']);
    }

    public function delete(User $user, Household $household): bool
    {
        return $household->owner_id === $user->id;
    }
}
