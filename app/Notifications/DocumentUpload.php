<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $employee = Employee::find($this->document->employee_id);
        return [
            'id' => $this->document->id,
            'type' => (isset($this->document->title)) ? 'feedback' : 'document',
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => (isset($this->document->title)) ? $this->document->title : $this->document->subject
        ];
    }
}
