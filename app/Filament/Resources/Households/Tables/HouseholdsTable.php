<?php

namespace App\Filament\Resources\Households\Tables;

use App\Filament\Resources\Households\Pages\ViewHousehold;
use App\Models\Household;
use App\Models\Rt;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HouseholdsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                // =====================================================
                // IDENTITAS KK
                // =====================================================

                TextColumn::make('no_kk')
                    ->label('Nomor KK')
                    ->description(
                        fn(Household $record) => $record->address ?: 'Alamat belum diisi'
                    )
                    ->weight('semibold')
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->where(function (Builder $query) use ($search) {
                                $query
                                    ->where('no_kk', 'like', "%{$search}%")
                                    ->orWhere('address', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(),

                TextColumn::make('head.full_name')
                    ->label('Kepala Keluarga')
                    ->placeholder('Belum ditentukan')
                    ->searchable(),

                // =====================================================
                // DATA UTAMA
                // =====================================================

                TextColumn::make('residents_count')
                    ->label('Anggota')
                    ->counts('residents')
                    ->formatStateUsing(fn($state) => $state . ' orang')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('rt.number')
                    ->label('RT / RW')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(
                        fn($state, Household $record) => $record->rt
                            ? "RT {$record->rt->number} / RW {$record->rt->rw->number}"
                            : '-'
                    ),

                // =====================================================
                // DATA TAMBAHAN
                // =====================================================

                TextColumn::make('pln_customer_number')
                    ->label('ID Pelanggan PLN')
                    ->placeholder('Belum diisi')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            // =========================================================
            // SEARCH
            // =========================================================

            ->searchPlaceholder('Cari nomor KK atau alamat...')
            ->searchDebounce('500ms')
            ->striped()

            // =========================================================
            // URUTAN DATA
            // =========================================================

            ->modifyQueryUsing(
                fn(Builder $query) => $query->orderBy('no_kk')
            )

            // =========================================================
            // PAGINATION
            // =========================================================

            ->paginated([10, 25, 50, 'all'])
            ->defaultPaginationPageOption(25)

            // =========================================================
            // FILTER
            // =========================================================

            ->filters([

                SelectFilter::make('rt')
                    ->label('RT')
                    ->options(
                        fn() => Rt::with('rw')
                            ->get()
                            ->mapWithKeys(
                                fn(Rt $rt) => [
                                    $rt->id => "RT {$rt->number} / RW {$rt->rw->number}",
                                ]
                            )
                    )
                    ->searchable()
                    ->preload(),

            ])

            // =========================================================
            // NAVIGASI & ACTION
            // =========================================================

            ->recordUrl(
                fn(Household $record): string =>
                ViewHousehold::getUrl(['record' => $record])
            )

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
