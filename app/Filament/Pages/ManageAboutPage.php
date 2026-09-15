<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Us Page';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 16;

    protected static string $view = 'filament.pages.manage-about-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'about_title'    => Setting::get('about_title', 'About Jubilee Direct'),
            'about_subtitle' => Setting::get('about_subtitle', 'Connecting shoppers worldwide with global e-commerce stores.'),
            'about_content'  => Setting::get('about_content', "Jubilee Direct was founded with a clear vision: to eliminate cross-border shopping barriers.\n\nWe simplify everything into a single, intuitive platform."),
            'about_mission'  => Setting::get('about_mission', 'To make global products accessible to anyone, anywhere by offering transparent pricing, secure procurement, and seamless doorstep delivery.'),
            'about_vision'   => Setting::get('about_vision', 'To become the premier global purchase-forwarding service trusted by millions for cross-border e-commerce solutions.'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('About Us Page Content')
                    ->description('Edit the main headers, story, mission, and vision displayed on the /about page.')
                    ->schema([
                        TextInput::make('about_title')
                            ->label('Main Page Title')
                            ->required(),
                        TextInput::make('about_subtitle')
                            ->label('Hero Subtitle')
                            ->required(),
                        Textarea::make('about_content')
                            ->label('Our Story & Overview')
                            ->rows(6)
                            ->helperText('Supports multiple paragraphs separated by blank lines.')
                            ->required(),
                        Textarea::make('about_mission')
                            ->label('Mission Statement')
                            ->rows(3)
                            ->required(),
                        Textarea::make('about_vision')
                            ->label('Vision Statement')
                            ->rows(3)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        Setting::set('about_title', $data['about_title'], 'About Us Page - Main Title', 'about_page');
        Setting::set('about_subtitle', $data['about_subtitle'], 'About Us Page - Hero Subtitle', 'about_page');
        Setting::set('about_content', $data['about_content'], 'About Us Page - Story & Overview', 'about_page');
        Setting::set('about_mission', $data['about_mission'], 'About Us Page - Mission Statement', 'about_page');
        Setting::set('about_vision', $data['about_vision'], 'About Us Page - Vision Statement', 'about_page');

        Notification::make()
            ->title('About Us Settings Saved')
            ->body('The About Us page content has been updated successfully.')
            ->success()
            ->send();
    }
}
