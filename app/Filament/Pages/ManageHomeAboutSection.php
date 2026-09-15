<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageHomeAboutSection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Main Page About Section';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 17;

    protected static string $view = 'filament.pages.manage-home-about-section';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'home_about_visible'  => Setting::get('home_about_visible', 'true'),
            'home_about_title'    => Setting::get('home_about_title', 'Shopping Internationally Made Effortless'),
            'home_about_subtitle' => Setting::get('home_about_subtitle', 'We buy products directly from top global stores and deliver them straight to your doorstep.'),
            'home_about_content'  => Setting::get('home_about_content', 'Jubilee Direct bridges the gap between international retailers and global shoppers. Simply provide a link from Amazon, eBay, or any major online store, and our procurement team will securely handle payment, customs clearance, and fast door-to-door delivery with total fee transparency.'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Main Page About Section Settings')
                    ->description('Manage the About/Overview section displayed on the home page (/).')
                    ->schema([
                        Select::make('home_about_visible')
                            ->label('Section Visibility')
                            ->options([
                                'true'  => 'Visible (Show section on home page)',
                                'false' => 'Hidden (Hide section from home page)',
                            ])
                            ->required(),
                        TextInput::make('home_about_title')
                            ->label('Section Title')
                            ->required(),
                        TextInput::make('home_about_subtitle')
                            ->label('Section Subtitle')
                            ->required(),
                        Textarea::make('home_about_content')
                            ->label('Section Description')
                            ->rows(5)
                            ->helperText('Supports multiple paragraphs separated by blank lines.')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        Setting::set('home_about_visible', $data['home_about_visible'], 'Show About Section on Main Page', 'home_page');
        Setting::set('home_about_title', $data['home_about_title'], 'Main Page About Section - Title', 'home_page');
        Setting::set('home_about_subtitle', $data['home_about_subtitle'], 'Main Page About Section - Subtitle', 'home_page');
        Setting::set('home_about_content', $data['home_about_content'], 'Main Page About Section - Description', 'home_page');

        Notification::make()
            ->title('Main Page Section Saved')
            ->body('The home page About section settings have been updated successfully.')
            ->success()
            ->send();
    }
}
