<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name'        => Setting::get('site_name', config('app.name')),
            'site_tagline'     => Setting::get('site_tagline'),
            'contact_email'    => Setting::get('contact_email'),
            'contact_phone'    => Setting::get('contact_phone'),
            'contact_address'  => Setting::get('contact_address'),
            'facebook_url'     => Setting::get('facebook_url'),
            'twitter_url'      => Setting::get('twitter_url'),
            'instagram_url'    => Setting::get('instagram_url'),
            'youtube_url'      => Setting::get('youtube_url'),
            'robots_txt'       => Setting::get('robots_txt', "User-agent: *\nAllow: /"),
            'google_analytics' => Setting::get('google_analytics'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')->required(),
                                Forms\Components\TextInput::make('site_tagline'),
                                Forms\Components\FileUpload::make('logo')
                                    ->image()->directory('settings')->label('Logo'),
                                Forms\Components\FileUpload::make('favicon')
                                    ->image()->directory('settings')->label('Favicon'),
                                Forms\Components\TextInput::make('contact_email')->email(),
                                Forms\Components\TextInput::make('contact_phone'),
                                Forms\Components\Textarea::make('contact_address')->rows(2),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->schema([
                                Forms\Components\TextInput::make('facebook_url')->url()->label('Facebook'),
                                Forms\Components\TextInput::make('twitter_url')->url()->label('X (Twitter)'),
                                Forms\Components\TextInput::make('instagram_url')->url()->label('Instagram'),
                                Forms\Components\TextInput::make('youtube_url')->url()->label('YouTube'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO & Analytics')
                            ->schema([
                                Forms\Components\Textarea::make('robots_txt')->label('robots.txt')->rows(5)->columnSpanFull(),
                                Forms\Components\Textarea::make('google_analytics')->label('Google Analytics / Head Scripts')->rows(4)->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        Notification::make()->title('Settings saved.')->success()->send();
    }
}
