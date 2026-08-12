<?php

namespace App\Filament\Resources\Rws\Pages;

use App\Filament\Resources\Rws\RwResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRw extends ViewRecord
{
    protected static string $resource = RwResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
