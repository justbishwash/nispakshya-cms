<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    public function getTabs(): array
    {
        return [
            'all'      => Tab::make('All'),
            'pending'  => Tab::make('Pending')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'pending')),
            'approved' => Tab::make('Approved')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'approved')),
            'spam'     => Tab::make('Spam')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'spam')),
        ];
    }
}
