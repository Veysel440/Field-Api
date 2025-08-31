<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attachment;

class AttachmentPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin','tech','viewer']); }
    public function view(User $user, Attachment $m): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $user->hasAnyRole(['admin','tech']); }
    public function delete(User $user, Attachment $m): bool { return $user->hasRole('admin'); }
}
