<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlanningExport implements FromArray, WithHeadings
{
    public function __construct(
        private readonly Collection $zones,
        private readonly Collection $windows
    ) {
    }

    public function headings(): array
    {
        return [
            'TYPE',
            'NAME',
            'DESCRIPTION',
            'STATUS',
            'START_DATE',
            'END_DATE',
            'IS_ACTIVE',
            'ASSIGNED_USERS',
            'ASSIGNED_CARS',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->windows as $window) {
            $rows[] = [
                'WINDOW',
                (string) ($window->name ?? ''),
                '',
                '',
                (string) $window->start_date,
                (string) $window->end_date,
                $window->is_active ? '1' : '0',
                '',
                '',
            ];
        }

        foreach ($this->zones as $zone) {
            $users = $zone->users?->map(fn ($u) => trim($u->name . ($u->service ? ' - ' . $u->service : '')))->implode(' | ') ?? '';
            $cars = $zone->cars?->map(fn ($c) => trim($c->name . ' (' . $c->matricule . ')'))->implode(' | ') ?? '';

            $rows[] = [
                'ZONE',
                (string) $zone->name,
                (string) ($zone->description ?? ''),
                (string) $zone->status,
                '',
                '',
                '',
                $users,
                $cars,
            ];
        }

        return $rows;
    }
}

