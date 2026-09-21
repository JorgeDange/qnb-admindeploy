<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function viewAny(Admin $auth): bool
    {
        return $auth->role === 'super_admin';
    }

    public function create(Admin $auth): bool
    {
        return $auth->role === 'super_admin';
    }

    public function update(Admin $auth, Admin $target): bool
    {
        return $auth->role === 'super_admin' || $auth->id === $target->id;
    }

    public function delete(Admin $auth, Admin $target): bool
    {
        return $auth->role === 'super_admin' && $auth->id !== $target->id;
    }

    public function toggleAtivo(Admin $auth, Admin $target): bool
    {
        return $auth->role === 'super_admin' && $auth->id !== $target->id;
    }

    public function resetPassword(Admin $auth, Admin $target): bool
    {
        return $auth->role === 'super_admin' && $auth->id !== $target->id;
    }
}
