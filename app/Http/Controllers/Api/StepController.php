<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreStepsRequest;
use App\Services\StepRecordService;
use Illuminate\Http\JsonResponse;

class StepController extends Controller
{
    public function __invoke(StoreStepsRequest $request, StepRecordService $service): JsonResponse
    {
        $result = $service->storeFromShortcut($request->user(), $request->stepRecords());

        return response()->json([
            'ok' => true,
            'saved' => $result->saved(),
            'created' => $result->created,
            'updated' => $result->updated,
        ]);
    }
}
