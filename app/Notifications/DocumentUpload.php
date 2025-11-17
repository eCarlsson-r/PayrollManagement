<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use App\Models\Employee;

class DocumentUpload extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->document = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $employee = Employee::find($this->document->employee_id);
        return [
            'id' => $this->document->id,
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => $this->document->subject
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->document->subject)
            ->body($employee->first_name . ' ' . $employee->last_name . ' uploaded a new document.')
            ->action('View Document', 'document')
            ->data([
                'id' => $this->document->id,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'title' => $this->document->subject
            ])
            ->options(['TTL' => 1000]);
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
            // ->tag()
            // ->vibrate()
    }
}
