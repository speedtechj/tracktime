<?php

namespace App\Filament\Resources\TimeZoneResource\Pages;

use App\Filament\Resources\TimeZoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeZone extends EditRecord
{
    protected static string $resource = TimeZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
