<?php

namespace Paymenter\Extensions\Others\Gifts\Policies;

use App\Models\User;
use Paymenter\Extensions\Others\Gifts\Models\Gift;

class GiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.gifts.view');
    }

    public function view(User $user, Gift $gift): bool
    {
        return $user->hasPermission('admin.gifts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.gifts.create');
    }

    public function update(User $user, Gift $gift): bool
    {
        return $user->hasPermission('admin.gifts.update');
    }

    public function delete(User $user, Gift $gift): bool
    {
        return $user->hasPermission('admin.gifts.delete');
    }
}
