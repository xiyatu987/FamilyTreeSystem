<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ziwei;

class ZiweiPolicy
{
    public function view(User $user, Ziwei $ziwei)
    {
        return $user->id === $ziwei->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Ziwei $ziwei)
    {
        return $user->id === $ziwei->user_id;
    }

    public function delete(User $user, Ziwei $ziwei)
    {
        return $user->id === $ziwei->user_id;
    }
}
