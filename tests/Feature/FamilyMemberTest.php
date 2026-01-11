<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyMemberTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * 测试用户可以创建家族成员
     */
    public function test_user_can_create_family_member()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/family-members', [
            'name' => '张三',
            'gender' => 'male',
            'birth_date' => '1980-01-01',
            'father_id' => null,
            'mother_id' => null,
            'spouse_id' => null,
            'ziwei_id' => null,
            'generation' => 1,
            'description' => '家族成员测试',
            'user_id' => $user->id
        ]);
        
        $response->assertRedirect('/family-members');
        $this->assertDatabaseHas('family_members', ['name' => '张三']);
    }
    
    /**
     * 测试用户可以查看家族成员列表
     */
    public function test_user_can_view_family_members()
    {
        $user = User::factory()->create();
        FamilyMember::factory()->create(['user_id' => $user->id, 'name' => '李四']);
        
        $response = $this->actingAs($user)->get('/family-members');
        
        $response->assertStatus(200);
        $response->assertSee('李四');
    }
    
    /**
     * 测试用户可以查看家族成员详情
     */
    public function test_user_can_view_family_member_details()
    {
        $user = User::factory()->create();
        $member = FamilyMember::factory()->create(['user_id' => $user->id, 'name' => '王五']);
        
        $response = $this->actingAs($user)->get('/family-members/' . $member->id);
        
        $response->assertStatus(200);
        $response->assertSee('王五');
    }
    
    /**
     * 测试用户可以更新家族成员信息
     */
    public function test_user_can_update_family_member()
    {
        $user = User::factory()->create();
        $member = FamilyMember::factory()->create(['user_id' => $user->id, 'name' => '赵六']);
        
        $response = $this->actingAs($user)->put('/family-members/' . $member->id, [
            'name' => '赵六更新',
            'gender' => 'male',
            'birth_date' => '1985-01-01',
            'father_id' => null,
            'mother_id' => null,
            'spouse_id' => null,
            'ziwei_id' => null,
            'generation' => 1,
            'description' => '更新后的家族成员测试',
            'user_id' => $user->id
        ]);
        
        $response->assertRedirect('/family-members');
        $this->assertDatabaseHas('family_members', ['name' => '赵六更新']);
    }
    
    /**
     * 测试用户可以删除家族成员
     */
    public function test_user_can_delete_family_member()
    {
        $user = User::factory()->create();
        $member = FamilyMember::factory()->create(['user_id' => $user->id, 'name' => '孙七']);
        
        $response = $this->actingAs($user)->delete('/family-members/' . $member->id);
        
        $response->assertRedirect('/family-members');
        $this->assertDatabaseMissing('family_members', ['name' => '孙七']);
    }
    
    /**
     * 测试用户只能看到自己的家族成员
     */
    public function test_user_can_only_see_own_family_members()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // 为用户1创建家族成员
        FamilyMember::factory()->create(['user_id' => $user1->id, 'name' => '用户1的成员']);
        
        // 为用户2创建家族成员
        FamilyMember::factory()->create(['user_id' => $user2->id, 'name' => '用户2的成员']);
        
        // 用户1登录查看家族成员列表
        $response = $this->actingAs($user1)->get('/family-members');
        
        $response->assertStatus(200);
        $response->assertSee('用户1的成员');
        $response->assertDontSee('用户2的成员');
    }
}
