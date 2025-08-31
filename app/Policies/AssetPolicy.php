<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;

class AssetPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin','tech','viewer']); }
    public function view(User $user, Asset $m): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $user->can('asset.create') || $user->hasAnyRole(['admin','tech']); }
    public function update(User $user, Asset $m): bool { return $user->can('asset.update') || $user->hasAnyRole(['admin','tech']); }
    public function delete(User $user, Asset $m): bool { return $user->can('asset.delete') || $user->hasRole('admin'); }
}
