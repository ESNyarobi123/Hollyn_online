<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HostingCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Service $service) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $pass = null;
        try { $pass = decrypt((string) $this->service->webuzo_temp_password_enc); } catch (\Throwable) {}

        $mailMessage = (new MailMessage)
            ->subject('Your Hosting is Active')
            ->greeting('Karibu!')
            ->line('Akaunti yako ya hosting imeundwa na iko tayari.')
            ->line('Panel URL: '.$this->service->enduser_url)
            ->line('Username: '.$this->service->webuzo_username);

        if ($pass) {
            $mailMessage->line('Temporary Password: '.$pass);
        }

        return $mailMessage
            ->action('Open Panel', $this->service->enduser_url)
            ->line('Ukipenda, badili password mara tu ukiingia.');
    }
}
