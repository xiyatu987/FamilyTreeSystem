<?php

namespace App\Providers;

use App\Models\FamilyMember;
use App\Models\Ziwei;
use App\Models\Clan;
use App\Models\AncestralHall;
use App\Models\MigrationRecord;
use App\Models\Grave;
use App\Models\FamilyRule;
use App\Models\ClanActivity;
use App\Policies\FamilyMemberPolicy;
use App\Policies\ZiweiPolicy;
use App\Policies\ClanPolicy;
use App\Policies\AncestralHallPolicy;
use App\Policies\MigrationRecordPolicy;
use App\Policies\GravePolicy;
use App\Policies\FamilyRulePolicy;
use App\Policies\ClanActivityPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 应用的策略映射。
     *
     * @var array
     */
    protected $policies = [
        FamilyMember::class => FamilyMemberPolicy::class,
        Ziwei::class => ZiweiPolicy::class,
        Clan::class => ClanPolicy::class,
        AncestralHall::class => AncestralHallPolicy::class,
        MigrationRecord::class => MigrationRecordPolicy::class,
        Grave::class => GravePolicy::class,
        FamilyRule::class => FamilyRulePolicy::class,
        ClanActivity::class => ClanActivityPolicy::class,
    ];

    /**
     * 注册认证/授权服务。
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}