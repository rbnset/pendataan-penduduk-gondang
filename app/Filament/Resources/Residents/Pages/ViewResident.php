<?php

namespace App\Filament\Resources\Residents\Pages;

use App\Filament\Resources\Households\HouseholdResource;
use App\Filament\Resources\Residents\ResidentResource;
use App\Models\Resident;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResident extends ViewRecord
{
    protected static string $resource = ResidentResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('viewHousehold')
                ->label('Lihat Kartu Keluarga')
                ->icon('heroicon-o-home')
                ->color('gray')
                ->url(
                    fn(Resident $record): ?string => $record->household_id
                        ? HouseholdResource::getUrl('view', ['record' => $record->household_id])
                        : null
                )
                ->disabled(
                    fn(Resident $record): bool => blank($record->household_id)
                )
                ->tooltip(
                    fn(Resident $record): ?string => blank($record->household_id)
                        ? 'Warga ini belum terhubung ke kartu keluarga manapun'
                        : null
                ),

            EditAction::make()
                ->label('Edit Data'),

        ];
    }
}
