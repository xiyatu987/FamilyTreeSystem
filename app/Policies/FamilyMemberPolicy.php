<?php

namespace App\Policies;

use App\Models\FamilyMember;
use App\Models\User;

class FamilyMemberPolicy
{
    /**
     * 判断用户是否可以查看家族成员
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FamilyMember  $familyMember
     * @return bool
     */
    public function view(User $user, FamilyMember $familyMember)
    {
        return $user->id === $familyMember->user_id;
    }

    /**
     * 判断用户是否可以更新家族成员
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FamilyMember  $familyMember
     * @return bool
     */
    public function update(User $user, FamilyMember $familyMember)
    {
        return $user->id === $familyMember->user_id;
    }

    /**
     * 判断用户是否可以删除家族成员
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FamilyMember  $familyMember
     * @return bool
     */
    public function delete(User $user, FamilyMember $familyMember)
    {
        return $user->id === $familyMember->user_id;
    }
}