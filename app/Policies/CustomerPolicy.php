<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin','tech','viewer']); }
    public function view(User $user, Customer $m): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $user->can('customer.create') || $user->hasAnyRole(['admin','tech']); }
    public function update(User $user, Customer $m): bool { return $user->can('customer.update') || $user->hasAnyRole(['admin','tech']); }
    public function delete(User $user, Customer $m): bool { return $user->can('customer.delete') || $user->hasRole('admin'); }
}
