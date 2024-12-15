<?php

namespace App\Filament\Resources\TracktimeResource\Pages;

use App\Filament\Resources\TracktimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTracktime extends EditRecord
{
    protected static string $resource = TracktimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
