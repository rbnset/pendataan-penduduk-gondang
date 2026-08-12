<?php

namespace App\Filament\Resources\Marriages\Pages;

use App\Filament\Resources\Marriages\MarriageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMarriage extends ViewRecord
{
    protected static string $resource = MarriageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
