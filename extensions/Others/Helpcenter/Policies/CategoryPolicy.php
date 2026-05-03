<?php

namespace Paymenter\Extensions\Others\Helpcenter\Policies;

use App\Models\User;
use Paymenter\Extensions\Others\Helpcenter\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.view');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.update');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.categories.delete');
    }
}

