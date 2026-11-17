<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static bool $isDiscovered = false;

    protected static ?string $title = 'Overview';

    protected static ?string $navigationLabel = 'Overview';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -10;

    public function getSubheading(): ?string
    {
        $name = auth()->user()?->name;

        return $name
            ? "Welcome back, {$name}. Here is what is happening across Evently."
            : 'Here is what is happening across Evently.';
    }
}
