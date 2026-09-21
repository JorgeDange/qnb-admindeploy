<?php

namespace App\Observers;

use App\Models\Admin;
use App\Services\ActivityLogService;

class AdminObserver
{
    public function created(Admin $admin): void
    {
        ActivityLogService::log('criar_admin', $admin, $admin->toArray());
    }

    public function updated(Admin $admin): void
    {
        ActivityLogService::log('editar_admin', $admin, [
            'antes' => $admin->getOriginal(),
            'depois' => $admin->getChanges(),
        ]);
    }

    public function deleted(Admin $admin): void
    {
        ActivityLogService::log('apagar_admin', $admin, $admin->toArray());
    }
}
