<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_asset')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('deskripsi')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('kategori')
                    ->options([
                        'Software' => 'Software',
                        'Hosting' => 'Hosting',
                        'Hardware' => 'Hardware',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_pembelian')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_perpanjangan'),
                Forms\Components\Select::make('Tipe')
                ->options([
                    'Lifetime' => 'Lifetime',
                    'Berlangganan' => 'Berlangganan',
                ])
                ->required(),
                Forms\Components\Select::make('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Perpanjangan' => 'Perpanjangan',
                        'Tidak Aktif' => 'Tidak Aktif',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_asset')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori'),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pembelian')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_perpanjangan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Tipe'),
                Tables\Columns\TextColumn::make('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
