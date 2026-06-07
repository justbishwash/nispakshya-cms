<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make('All'),
            'published' => Tab::make('Published')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'published')),
            'pending'   => Tab::make('Pending')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'pending')),
            'draft'     => Tab::make('Drafts')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'draft')),
            'scheduled' => Tab::make('Scheduled')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'scheduled')),
        ];
    }
}
