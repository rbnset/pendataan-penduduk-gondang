<?php

namespace App\Filament\Resources\Rws\Pages;

use App\Filament\Resources\Rws\RwResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRw extends EditRecord
{
    protected static string $resource = RwResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading(fn() => "Hapus RW {$this->record->number}?")
                ->modalDescription(
                    fn() =>
                    "RW {$this->record->number} akan dihapus dari data wilayah. "
                        . "Tindakan ini tidak dapat dibatalkan."
                )
                ->modalSubmitActionLabel('Ya, hapus')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('RW berhasil dihapus')
                        ->body("RW {$this->record->number} telah dihapus dari data wilayah.")
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('RW berhasil diperbarui')
            ->body(
                "Data RW {$this->record->number} berhasil diperbarui."
            )
            ->duration(5000);
    }
}
