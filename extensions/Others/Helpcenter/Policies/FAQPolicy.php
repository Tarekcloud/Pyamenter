<?php

namespace Paymenter\Extensions\Others\Helpcenter\Policies;

use App\Models\User;
use Paymenter\Extensions\Others\Helpcenter\Models\FAQ;

class FAQPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.view');
    }

    public function view(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.create');
    }

    public function update(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.update');
    }

    public function delete(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.faqs.delete');
    }
}

