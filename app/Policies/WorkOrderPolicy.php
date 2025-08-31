<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin','tech','viewer']); }
    public function view(User $user, WorkOrder $wo): bool { return $this->viewAny($user); }


    public function update(User $user, WorkOrder $wo): bool
    {
        return $wo->assigned_user_id !== null && $user->id === $wo->assigned_user_id;
    }

    public function assign(User $user, WorkOrder $wo): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool { return $user->hasAnyRole(['admin','tech']); }
    public function delete(User $user, WorkOrder $wo): bool { return $user->hasRole('admin'); }
}
