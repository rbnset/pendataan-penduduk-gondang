<?php

namespace App\Filament\Resources\Rws\RelationManagers;

use App\Filament\Resources\Households\HouseholdResource;
use App\Models\Rt;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RtsRelationManager extends RelationManager
{
    protected static string $relationship = 'rts';

    protected static ?string $title = 'RT dalam Wilayah Ini';

    protected static ?string $modelLabel = 'RT';

    // =============================================================
    // Data RT cukup sederhana (nomor + ketua), jadi form ini dipakai
    // langsung untuk create & edit inline — tidak perlu resource/
    // halaman terpisah seperti Resident. rw_id otomatis terisi
    // karena dibuat lewat relasi RW ini.
    // =============================================================

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('number')
                    ->label('Nomor RT')
                    ->placeholder('Contoh: 01')
                    ->required(),

                Select::make('chairman_resident_id')
                    ->relationship('chairman', 'full_name')
                    ->label('Ketua RT')
                    ->placeholder('Pilih ketua RT')
                    ->helperText('Opsional — biasanya baru bisa dipilih setelah warga di RT ini terdaftar.')
                    ->searchable()
                    ->preload()
                    ->default(null),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')

            ->columns([

                TextColumn::make('number')
                    ->label('RT')
                    ->formatStateUsing(fn($state) => "RT {$state}")
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('chairman.full_name')
                    ->label('Ketua RT')
                    ->placeholder('Belum ditentukan')
                    ->searchable(),

                TextColumn::make('households_count')
                    ->label('Jumlah KK')
                    ->counts('households')
                    ->formatStateUsing(fn($state) => $state . ' KK')
                    ->badge()
                    ->color('info')
                    ->sortable(),

            ])

            ->modifyQueryUsing(
                fn(Builder $query) => $query->orderBy('number')
            )

            ->headerActions([
                CreateAction::make()
                    ->label('Tambah RT')
                    ->modalHeading('Tambah RT'),
            ])

            ->recordActions([

                // Jalan pintas ke daftar KK yang sudah terfilter RT ini
                Action::make('lihatKk')
                    ->label('Lihat KK')
                    ->icon('heroicon-o-home')
                    ->color('gray')
                    ->url(
                        fn(Rt $record): string => HouseholdResource::getUrl('index', [
                            'tableFilters' => [
                                'rt' => ['value' => $record->id],
                            ],
                        ])
                    ),

                EditAction::make(),

                DeleteAction::make(),

            ]);
    }
}
