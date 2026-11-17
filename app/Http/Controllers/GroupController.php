<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(Request $request): View
    {
        $groups = $request->user()->groups()->get();

        return view('groups.index', ['groups' => $groups]);
    }

    public function store(StoreGroupRequest $request, GroupService $service): RedirectResponse
    {
        $service->create($request->user(), $request->validated('name'));

        return redirect()->route('groups.index')->with('status', 'グループを作成しました。');
    }
}
