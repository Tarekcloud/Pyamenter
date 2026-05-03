<?php

namespace Paymenter\Extensions\Others\Helpcenter\Policies;

use App\Models\User;
use Paymenter\Extensions\Others\Helpcenter\Models\Link;

class LinkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.links.view');
    }

    public function view(User $user, Link $link): bool
    {
        return $user->hasPermission('admin.helpcenter.links.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.links.create');
    }

    public function update(User $user, Link $link): bool
    {
        return $user->hasPermission('admin.helpcenter.links.update');
    }

    public function delete(User $user, Link $link): bool
    {
        return $user->hasPermission('admin.helpcenter.links.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.links.delete');
    }
}

