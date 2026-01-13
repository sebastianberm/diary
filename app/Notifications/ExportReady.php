<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportReady extends Notification
{
    use Queueable;

    public $downloadUrl;
    public $filename;

    /**
     * Create a new notification instance.
     */
    public function __construct($downloadUrl, $filename)
    {
        $this->downloadUrl = $downloadUrl;
        $this->filename = $filename;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Add 'mail' if mail is configured
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Diary Export is Ready')
            ->line('Your diary export has been generated successfully.')
            ->action('Download PDF', $this->downloadUrl)
            ->line('The link will be active as long as the file exists on the server.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your export ' . $this->filename . ' is ready.',
            'action_url' => $this->downloadUrl,
            'type' => 'export'
        ];
    }
}
