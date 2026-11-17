<?php

namespace App\Http\Controllers;

use App\Services\StepTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StepTokenController extends Controller
{
    public function store(Request $request, StepTokenService $tokens): RedirectResponse
    {
        $plain = $tokens->issue($request->user());

        return redirect()->route('link.show')->with('step_token_plain', $plain);
    }
}
