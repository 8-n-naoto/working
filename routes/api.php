<?php

use App\Http\Controllers\Api\StepController;
use App\Http\Middleware\AuthenticateStepToken;
use App\Http\Middleware\ThrottleStepsUser;
use Illuminate\Support\Facades\Route;

Route::post('/steps', StepController::class)
    ->middleware(['throttle:steps-ip', AuthenticateStepToken::class, ThrottleStepsUser::class])
    ->name('api.steps.store');
