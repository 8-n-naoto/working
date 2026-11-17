<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualStepRequest;
use App\Services\StepRecordService;
use Illuminate\Http\RedirectResponse;

class ManualStepController extends Controller
{
    public function store(StoreManualStepRequest $request, StepRecordService $service): RedirectResponse
    {
        $service->storeManual($request->user(), $request->stepRecord());

        return redirect()->route('link.show')->with('status', '歩数を保存しました。');
    }
}
