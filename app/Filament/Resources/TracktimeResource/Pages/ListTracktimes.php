<?php

namespace App\Filament\Resources\TracktimeResource\Pages;

use App\Filament\Resources\TracktimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTracktimes extends ListRecords
{
    protected static string $resource = TracktimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
