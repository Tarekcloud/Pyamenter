<?php

namespace Paymenter\Extensions\Others\Helpcenter\Policies;

use App\Models\User;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.view');
    }

    public function view(User $user, Article $article): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.create');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.update');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.helpcenter.articles.delete');
    }
}

