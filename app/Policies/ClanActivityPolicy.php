<?php

namespace App\Policies;

use App\Models\ClanActivity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClanActivityPolicy
{
    use HandlesAuthorization;

    // 确定用户是否可以查看宗族活动
    public function view(User $user, ClanActivity $clanActivity)
    {
        return $user->id === $clanActivity->user_id;
    }

    // 确定用户是否可以创建宗族活动
    public function create(User $user)
    {
        return true;
    }

    // 确定用户是否可以更新宗族活动
    public function update(User $user, ClanActivity $clanActivity)
    {
        return $user->id === $clanActivity->user_id;
    }

    // 确定用户是否可以删除宗族活动
    public function delete(User $user, ClanActivity $clanActivity)
    {
        return $user->id === $clanActivity->user_id;
    }
}
