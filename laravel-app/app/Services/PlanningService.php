<?php

namespace App\Services;

use App\Models\EventSlot;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class PlanningService
{
    public function slotsBetween(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return EventSlot::query()
            ->whereBetween('start', [$start, $end])
            ->orderBy('start')
            ->with('volunteers')
            ->get();
    }

    public function volunteerLoadByDay(CarbonImmutable $start, CarbonImmutable $end): array
    {
        return $this->slotsBetween($start, $end)
            ->groupBy(fn (EventSlot $slot) => $slot->start->format('Y-m-d'))
            ->map(fn ($slots) => $slots->map(function (EventSlot $slot) {
                return [
                    'id' => $slot->id,
                    'label' => sprintf('%s-%s', $slot->start->format('H:i'), $slot->end?->format('H:i')),
                    'volunteers' => $slot->volunteers->count(),
                    'public' => $slot->public,
                    'name' => $slot->name,
                ];
            })->values()->all())
            ->all();
    }
}
