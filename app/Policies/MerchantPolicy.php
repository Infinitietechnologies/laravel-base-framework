<?php

namespace App\Policies;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MerchantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Merchant $merchant): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Merchant $merchant): bool
    {
        $role = $user->getRoleNames();
        if (isset($role[0]) && !empty($role[0]) && $role[0] == "super_admin") {
            return true;
        }
        return $user->id === $merchant->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Merchant $merchant): bool
    {
        $role = $user->getRoleNames();
        if (isset($role[0]) && !empty($role[0]) && $role[0] == "super_admin") {
            return true;
        }
        return $user->id === $merchant->user_id;
    }
}
