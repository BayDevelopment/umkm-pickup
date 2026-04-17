<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationPendingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registrasi Berhasil - Menunggu Persetujuan')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Registrasi UMKM Anda berhasil.')
            ->line('Akun Anda sedang menunggu verifikasi dari admin.')
            ->line('Kami akan menghubungi Anda setelah akun disetujui.')
            ->salutation('Terima kasih, Tim Trendora');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
