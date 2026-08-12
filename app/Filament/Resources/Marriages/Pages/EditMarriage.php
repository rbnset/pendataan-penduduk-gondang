<?php

namespace App\Filament\Resources\Marriages\Pages;

use App\Filament\Resources\Marriages\MarriageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMarriage extends EditRecord
{
    protected static string $resource = MarriageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['is_divorced'])) {
            $data['divorce_certificate_number'] = null;
            $data['divorce_date'] = null;
        }

        unset($data['is_divorced']); // hapus field virtual sebelum save

        return $data;
    }
}
