<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseAuth;
use Filament\Schemas\Components\Component;

class Login extends BaseAuth
{
    /**
     * Tentukan lokasi file view custom (blade) untuk halaman login ini
     */
    protected string $view = 'filament.pages.auth.login';

    /**
     * Mengubah label dan placeholder input Email
     */
    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label('Email')
            ->placeholder('Masukkan alamat email anda...');
    }

    /**
     * Mengubah label dan placeholder input Password
     */
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label('Password')
            ->placeholder('Masukkan kata sandi/password anda...');
    }

    /**
     * Mengubah label checkbox Remember Me
     */
    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->label('Ingat saya');
    }
}
