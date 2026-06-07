<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Comment;
use App\Models\PageView;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayViews = PageView::whereDate('viewed_date', today())->count();
        $totalViews = PageView::count();

        return [
            Stat::make('Total Articles', Article::count())
                ->description('All time')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Published', Article::where('status', 'published')->count())
                ->description('Live articles')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pending Review', Article::where('status', 'pending')->count())
                ->description('Awaiting approval')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Today\'s Views', number_format($todayViews))
                ->description('Total: ' . number_format($totalViews))
                ->icon('heroicon-o-eye')
                ->color('info'),

            Stat::make('Comments', Comment::where('status', 'pending')->count())
                ->description('Pending moderation')
                ->icon('heroicon-o-chat-bubble-left')
                ->color('danger'),

            Stat::make('Users', User::where('is_active', true)->count())
                ->description('Active newsroom members')
                ->icon('heroicon-o-users')
                ->color('gray'),
        ];
    }
}
