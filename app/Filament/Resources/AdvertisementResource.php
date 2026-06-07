<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Advertising';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->required()->columnSpanFull(),

                Forms\Components\Select::make('position')
                    ->options(Advertisement::positionLabels())
                    ->required(),

                Forms\Components\Select::make('type')
                    ->options(['image' => 'Image Ad', 'html' => 'HTML Ad'])
                    ->default('image')
                    ->live()
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('ads')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('html_code')
                    ->label('HTML / Embed Code')
                    ->rows(5)
                    ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('link_url')
                    ->label('Click URL')
                    ->url(),

                Forms\Components\Toggle::make('open_new_tab')->default(true),

                Forms\Components\DateTimePicker::make('starts_at')->label('Start Date'),
                Forms\Components\DateTimePicker::make('ends_at')->label('End Date'),

                Forms\Components\Toggle::make('is_active')->default(true)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->formatStateUsing(fn ($state) => Advertisement::positionLabels()[$state] ?? $state),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('impressions')->sortable(),
                Tables\Columns\TextColumn::make('clicks')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('ends_at')->date('M d, Y')->label('Expires'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->options(Advertisement::positionLabels()),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit'   => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
