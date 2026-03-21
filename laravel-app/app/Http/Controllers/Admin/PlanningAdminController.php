<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSlot;
use Illuminate\Contracts\View\View;

class PlanningAdminController extends Controller
{
    public function index(): View
    {
        $slots = EventSlot::query()->withCount('volunteers')->orderBy('start')->paginate(30);

        return view('admin.planning.index', compact('slots'));
    }
}
