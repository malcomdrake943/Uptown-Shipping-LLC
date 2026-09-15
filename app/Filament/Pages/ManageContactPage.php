<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageContactPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contact Us Page';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 18;

    protected static string $view = 'filament.pages.manage-contact-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'contact_title'         => Setting::get('contact_title', 'Get in Touch with Jubilee Direct'),
            'contact_subtitle'      => Setting::get('contact_subtitle', 'Have questions about an order, shipping options, or custom quotes? Our team is here to assist you.'),
            'contact_email'         => Setting::get('contact_email', 'support@jubileedirect.com'),
            'contact_phone'         => Setting::get('contact_phone', '+1 (800) 555-0199'),
            'contact_whatsapp'      => Setting::get('contact_whatsapp', '+1 (800) 555-0199'),
            'contact_address'       => Setting::get('contact_address', '123 Commerce Boulevard, Suite 400, New York, NY 10001, United States'),
            'contact_working_hours' => Setting::get('contact_working_hours', "Monday – Friday: 9:00 AM – 6:00 PM EST\nSaturday: 10:00 AM – 4:00 PM EST\nSunday: Closed"),
            'contact_form_intro'    => Setting::get('contact_form_intro', 'Send us a message and our procurement specialists will get back to you within 24 hours.'),
            'contact_shipping_info' => Setting::get('contact_shipping_info', "We offer transparent cross-border shipping solutions tailored to your cargo size and urgency:\n\n• Express Air (3–7 Days) – Expedited courier for fast doorstep delivery\n• Standard Air (7–14 Days) – Reliable and economical global air freight\n• Sea Freight (4–8 Weeks) – Best value for bulk, heavy, or oversized items\n\nOur system calculates the exact shipping charge after you submit your product link and package specifications."),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hero & Page Header')
                    ->description('Main title and subtitle displayed at the top of the /contact page.')
                    ->schema([
                        TextInput::make('contact_title')
                            ->label('Main Page Title')
                            ->required(),
                        TextInput::make('contact_subtitle')
                            ->label('Hero Subtitle')
                            ->required(),
                    ]),

                Section::make('Direct Contact Information')
                    ->description('Contact channels displayed on the contact cards.')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('contact_email')
                                ->label('Support Email Address')
                                ->email()
                                ->required(),
                            TextInput::make('contact_phone')
                                ->label('Support Phone Number')
                                ->required(),
                            TextInput::make('contact_whatsapp')
                                ->label('WhatsApp Contact')
                                ->helperText('Phone number with country code for direct WhatsApp chats.')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('contact_address')
                                ->label('Office / Headquarters Address')
                                ->rows(3)
                                ->required(),
                            Textarea::make('contact_working_hours')
                                ->label('Working & Operating Hours')
                                ->rows(3)
                                ->helperText('Operating schedule (supports multiple lines).')
                                ->required(),
                        ]),
                    ]),

                Section::make('Shipping Options & Custom Quotes Information')
                    ->description('Information displayed regarding delivery speeds (Express Air, Standard Air, Sea Freight) and quote calculation.')
                    ->schema([
                        Textarea::make('contact_shipping_info')
                            ->label('Shipping Speeds & Quotes Notice')
                            ->rows(5)
                            ->helperText('Information explaining Express Air (3-7 Days), Standard Air (7-14 Days), and Sea Freight (4-8 Weeks).')
                            ->required(),
                    ]),

                Section::make('Contact Form & Inquiry Settings')
                    ->description('Introductory description displayed above the customer contact form.')
                    ->schema([
                        Textarea::make('contact_form_intro')
                            ->label('Form Intro Description')
                            ->rows(3)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        Setting::set('contact_title', $data['contact_title'], 'Contact Us Page - Main Title', 'contact_page');
        Setting::set('contact_subtitle', $data['contact_subtitle'], 'Contact Us Page - Hero Subtitle', 'contact_page');
        Setting::set('contact_email', $data['contact_email'], 'Contact Us Page - Support Email', 'contact_page');
        Setting::set('contact_phone', $data['contact_phone'], 'Contact Us Page - Support Phone', 'contact_page');
        Setting::set('contact_whatsapp', $data['contact_whatsapp'], 'Contact Us Page - WhatsApp Number', 'contact_page');
        Setting::set('contact_address', $data['contact_address'], 'Contact Us Page - Office Address', 'contact_page');
        Setting::set('contact_working_hours', $data['contact_working_hours'], 'Contact Us Page - Business Hours', 'contact_page');
        Setting::set('contact_shipping_info', $data['contact_shipping_info'], 'Contact Us Page - Shipping Methods Info', 'contact_page');
        Setting::set('contact_form_intro', $data['contact_form_intro'], 'Contact Us Page - Form Intro / Description', 'contact_page');

        Notification::make()
            ->title('Contact Us Settings Saved')
            ->body('The Contact Us page settings and contact information have been updated successfully.')
            ->success()
            ->send();
    }
}
