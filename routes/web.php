<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInviteCodeController;
use App\Http\Controllers\GroupJoinController;
use App\Http\Controllers\GroupLeaveController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\GroupMemberRemovalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ManualStepController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\StepTokenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'home' : 'login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', HomeController::class)->name('home');

    // [T-004] 連携
    Route::get('/link', [LinkController::class, 'show'])
        ->middleware('cache.headers:no_store;private')
        ->name('link.show');
    Route::post('/link/token', [StepTokenController::class, 'store'])
        ->middleware('throttle:token-issue')
        ->name('link.token.store');

    // [T-005] グループ
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [GroupController::class, 'store'])->middleware('throttle:group-create')->name('groups.store');
    Route::post('/groups/join', GroupJoinController::class)->middleware('throttle:group-join')->name('groups.join');
    Route::post('/groups/{group}/invite-code', GroupInviteCodeController::class)
        ->whereNumber('group')
        ->middleware('can:regenerateInviteCode,group')
        ->name('groups.invite-code.regenerate');

    // [T-118] D-029 メンバー除外（オーナー）・退会（メンバー本人）
    Route::delete('/groups/{group}/members/{user}', GroupMemberRemovalController::class)
        ->whereNumber(['group', 'user'])
        ->middleware('can:removeMember,group,user')
        ->name('groups.members.destroy');
    Route::delete('/groups/{group}/membership', GroupLeaveController::class)
        ->whereNumber('group')
        ->middleware('can:leave,group')
        ->name('groups.leave');

    // [T-006] ホーム・ランキング・手動入力
    Route::get('/groups/{group}/ranking', RankingController::class)
        ->whereNumber('group')
        ->middleware('can:view,group')
        ->name('groups.ranking');
    Route::get('/groups/{group}/members/{user}', GroupMemberController::class)
        ->whereNumber(['group', 'user'])
        ->middleware('can:viewMember,group,user')
        ->name('groups.members.show');
    Route::post('/steps/manual', [ManualStepController::class, 'store'])
        ->middleware('throttle:manual-steps')
        ->name('steps.manual.store');
});

require __DIR__.'/auth.php';
