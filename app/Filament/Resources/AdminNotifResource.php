<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminNotifResource\Pages;
use App\Filament\Resources\AdminNotifResource\RelationManagers;
use App\Models\AdminNotif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class AdminNotifResource extends Resource
{
    protected static ?string $model = AdminNotif::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
       
                    Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(13)
                    ->minLength(10)
                    ->numeric()
                    ->inputMode('tel')
                    ->placeholder('8xxxxxxxxxx')
                    ->prefix('62')
                    ->formatStateUsing(fn ($state) => $state ? str_replace('62', '', $state) : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? '62' . $state : null),
                Forms\Components\Select::make('admin')
                    ->options([
                        'admin1' => 'admin1',
                        'admin2' => 'admin2',
                        'admin3' => 'admin3',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin'),
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
            'index' => Pages\ListAdminNotifs::route('/'),
            'create' => Pages\CreateAdminNotif::route('/create'),
            'edit' => Pages\EditAdminNotif::route('/{record}/edit'),
        ];
    }
}
