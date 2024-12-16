<?php

namespace App\Filament\Resources\TracktimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TracktimeResource;
use App\Filament\Resources\TracktimeResource\Widgets\TotalHours;

class ListTracktimes extends ListRecords
{
    protected static string $resource = TracktimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TotalHours::make(),
        ];
    }
}
