<?php

namespace App\Policies;

use App\Models\FamilyRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyRulePolicy
{
    use HandlesAuthorization;

    // 确定用户是否可以查看家族规则
    public function view(User $user, FamilyRule $familyRule)
    {
        return $user->id === $familyRule->user_id;
    }

    // 确定用户是否可以创建家族规则
    public function create(User $user)
    {
        return true;
    }

    // 确定用户是否可以更新家族规则
    public function update(User $user, FamilyRule $familyRule)
    {
        return $user->id === $familyRule->user_id;
    }

    // 确定用户是否可以删除家族规则
    public function delete(User $user, FamilyRule $familyRule)
    {
        return $user->id === $familyRule->user_id;
    }
}
