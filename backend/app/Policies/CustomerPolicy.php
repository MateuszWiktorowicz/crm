<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, array $data): bool
    {
    if ($user->hasRole('admin') || $user->hasRole('regeneration')) {
        return true;
    }

    if ($user->hasRole('saler') && isset($data['saler_marker']) && $data['saler_marker'] === $user->marker) {
        return true;
    }

    return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('regeneration') || $user->marker === $customer->saler_marker) {
            return true;
        }

        return false;
    }

   /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {

        if ($user->hasRole('admin') || $user->hasRole('regeneration') || $user->marker === $customer->saler_marker) {
            return true;
        }

        return false;
    }
}
