<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('article_id')
                ->relationship('article', 'title')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('commenter_name')->required(),
            Forms\Components\TextInput::make('commenter_phone'),

            Forms\Components\Textarea::make('body')->required()->rows(4)->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'spam'     => 'Spam',
                    'rejected' => 'Rejected',
                ])
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('commenter_name')->searchable(),
                Tables\Columns\TextColumn::make('commenter_phone'),
                Tables\Columns\TextColumn::make('article.title')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('body')->limit(60),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => ['spam', 'rejected'],
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'spam'     => 'Spam',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),

                Tables\Actions\Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->action(fn ($record) => $record->update(['status' => 'spam'])),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_all')
                    ->label('Approve Selected')
                    ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit'  => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
