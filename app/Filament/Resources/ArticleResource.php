<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Article')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(500)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                    $set('slug', Str::slug($state))
                                )
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(500)
                                ->unique(ignoreRecord: true)
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('author_id')
                                    ->label('Author')
                                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->default(auth()->id())
                                    ->required(),
                            ]),

                            Forms\Components\Textarea::make('excerpt')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('content')
                                ->required()
                                ->toolbarButtons([
                                    'attachFiles', 'blockquote', 'bold', 'bulletList',
                                    'codeBlock', 'h2', 'h3', 'italic', 'link',
                                    'orderedList', 'redo', 'strike', 'underline', 'undo',
                                ])
                                ->columnSpanFull(),

                            Forms\Components\Select::make('tags')
                                ->multiple()
                                ->relationship('tags', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Media')
                        ->schema([
                            Forms\Components\FileUpload::make('featured_image')
                                ->label('Featured Image')
                                ->image()
                                ->directory('articles/featured')
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('featured_image_caption')
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('video_url')
                                ->label('Video URL (YouTube / Facebook)')
                                ->url()
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Publishing')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft'     => 'Draft',
                                        'pending'   => 'Pending Review',
                                        'published' => 'Published',
                                        'scheduled' => 'Scheduled',
                                        'archived'  => 'Archived',
                                    ])
                                    ->default('draft')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date'),

                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label('Schedule Date'),
                            ]),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Toggle::make('is_breaking')
                                    ->label('Breaking News'),

                                Forms\Components\Toggle::make('is_trending')
                                    ->label('Trending'),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured'),
                            ]),

                            Forms\Components\DateTimePicker::make('breaking_expires_at')
                                ->label('Breaking News Expires At')
                                ->visible(fn (Forms\Get $get) => $get('is_breaking')),
                        ]),

                    Forms\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->maxLength(70)
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('seo_description')
                                ->label('Meta Description')
                                ->rows(2)
                                ->maxLength(160)
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('seo_keywords')
                                ->label('Keywords')
                                ->helperText('Comma separated keywords')
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('')
                    ->circular(false)
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->description(fn ($record) => $record->category?->name),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'draft',
                        'warning' => 'pending',
                        'success' => 'published',
                        'info'    => 'scheduled',
                        'danger'  => 'archived',
                    ]),

                Tables\Columns\IconColumn::make('is_breaking')
                    ->label('Breaking')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'pending'   => 'Pending Review',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                        'archived'  => 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_breaking')
                    ->label('Breaking News'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update([
                            'status'       => 'published',
                            'published_at' => now(),
                        ])),

                    Tables\Actions\BulkAction::make('draft')
                        ->label('Set to Draft')
                        ->icon('heroicon-o-pencil')
                        ->action(fn ($records) => $records->each->update(['status' => 'draft'])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}
