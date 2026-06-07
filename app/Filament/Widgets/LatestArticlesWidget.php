<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestArticlesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Articles')
            ->query(Article::latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('title')->limit(60)->url(fn ($record) => ArticleResource::getUrl('edit', ['record' => $record])),
                Tables\Columns\TextColumn::make('author.name')->label('Author'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'draft',
                        'warning' => 'pending',
                        'success' => 'published',
                        'info'    => 'scheduled',
                    ]),
                Tables\Columns\TextColumn::make('views'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ]);
    }
}
