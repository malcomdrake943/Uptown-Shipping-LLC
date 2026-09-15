<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use App\Models\User;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email address or Phone number')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $loginInput = trim($data['email']);
        
        // Strip non-digit characters except leading +
        $digitsOnly = preg_replace('/[^\d]/', '', $loginInput);

        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            // Find user by phone number
            $user = User::all()->first(function ($u) use ($loginInput, $digitsOnly) {
                if (empty($u->phone)) {
                    return false;
                }
                $userPhoneDigits = preg_replace('/[^\d]/', '', $u->phone);
                return $u->phone === $loginInput || ($digitsOnly && $userPhoneDigits === $digitsOnly);
            });

            if ($user) {
                return [
                    'email'    => $user->email,
                    'password' => $data['password'],
                ];
            }
        }

        return [
            'email'    => $loginInput,
            'password' => $data['password'],
        ];
    }
}
