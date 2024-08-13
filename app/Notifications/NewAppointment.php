<?php

namespace App\Notifications;

use App\Models\Appointments;
use Illuminate\Bus\Queueable;
use Illuminate\Console\View\Components\Line;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointment extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     * 
     */
    public function __construct(Appointments $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Appointment Scheduled on'.$this->appointment->date )
                    ->line('Hello,' . $this->appointment->staff->fullname)
                    ->line('You have a new appointment scheduled.')
                    ->line('Customer Name:' . $this->appointment->customer->fullname)
                    ->line('Service:' . $this->appointment->service->service_name)
                    ->line('Time:' . $this->appointment->time)
                    ->action('View Appointment', url('http://localhost:4200/staff-dashboard'))
                    ->line('Thank you!')
                    ->line('From : Admin');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'You have a new appointment scheduled on'. $this->appointment->date
        ];
    }
}
