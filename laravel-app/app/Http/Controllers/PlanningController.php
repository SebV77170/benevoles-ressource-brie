<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanningRegistrationRequest;
use App\Models\EventSlot;
use App\Services\PlanningService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PlanningController extends Controller
{
    public function __construct(private readonly PlanningService $planningService)
    {
    }

    public function index(): View
    {
        $start = CarbonImmutable::now()->startOfMonth()->startOfWeek();
        $end = $start->addWeeks(6)->endOfWeek();

        return view('planning.index', [
            'slotsByDay' => $this->planningService->volunteerLoadByDay($start, $end),
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function register(StorePlanningRegistrationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $slots = EventSlot::query()->whereIn('id', $request->validated('event_ids'))->get();

        foreach ($slots as $slot) {
            $user->eventSlots()->syncWithoutDetaching([$slot->id => ['fonction' => 'N/A']]);
        }

        return back()->with('status', 'Inscription enregistrée.');
    }
}
