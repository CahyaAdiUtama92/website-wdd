<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected string $view = 'filament.pages.auth.request-password-reset';

    public bool $isSubmitted = false;

    protected function getSentNotification(string $status): ?\Filament\Notifications\Notification
    {
        $this->isSubmitted = true;
        
        return null;
    }

    protected function getFailureNotification(string $status): ?\Filament\Notifications\Notification
    {
        $this->isSubmitted = true;
        
        return null;
    }
}
