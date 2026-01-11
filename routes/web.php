<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|\n| Here is where you can register web routes for your application. These\n| routes are loaded by the RouteServiceProvider within a group which\n| contains the "web" middleware group. Now create something great!\n|\n*/

// 首页路由
Route::get('/', function () {
    return view('welcome');
});

// 认证路由
Auth::routes();

// 登录后才能访问的路由
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // 家族成员管理路由
    Route::resource('family-members', App\Http\Controllers\FamilyMemberController::class);
    Route::get('family-members/search', [App\Http\Controllers\FamilyMemberController::class, 'search'])->name('family-members.search');
    Route::get('family-members/tree', [App\Http\Controllers\FamilyMemberController::class, 'familyTree'])->name('family-members.tree');
    
    // 字辈管理路由
    Route::resource('ziwei', App\Http\Controllers\ZiweiController::class);
    
    // 祠堂信息管理路由
    Route::resource('ancestral-halls', App\Http\Controllers\AncestralHallController::class);
    
    // 迁徙记录管理路由
    Route::resource('migration-records', App\Http\Controllers\MigrationRecordController::class);
    
    // 墓地信息管理路由
    Route::resource('graves', App\Http\Controllers\GraveController::class);
    
    // 家规家训管理路由
    Route::resource('family-rules', App\Http\Controllers\FamilyRuleController::class);
    
    // 宗族组织结构管理路由
    Route::resource('clans', App\Http\Controllers\ClanController::class);
    
    // 宗族活动记录路由
    Route::resource('clan-activities', App\Http\Controllers\ClanActivityController::class);
});

// 管理员路由
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/toggle-role', [App\Http\Controllers\AdminController::class, 'toggleRole'])->name('admin.toggleRole');
    Route::delete('/admin/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.deleteUser');
});