<?php

namespace App\Filament\Admin\Resources\EventAttendanceResource\Pages;

use App\Filament\Admin\Resources\EventAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListEventAttendances extends ListRecords
{
    protected static string $resource = EventAttendanceResource::class;
}
