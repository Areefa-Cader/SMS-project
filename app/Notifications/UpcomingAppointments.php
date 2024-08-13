<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tymon\JWTAuth\Claims\Subject;

class UpcomingAppointments extends Notification implements ShouldQueue
{
    use Queueable;

    private $appointmentData;

    /**
     * Create a new notification instance.
     */
    public function __construct($appointmentData)
    {
        $this->appointmentData = $appointmentData;
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
                    ->subject('Appointment Reminder')
                    ->line('Hello,' . $this->appointmentData->staff->fullname)
                    ->line('You have an upcoming appointment')
                    ->line('customer:'. $this->appointmentData->customer->fullname)
                    ->line('Service:'. $this->appointmentData->service->service_name)
                    ->line('Date:'. $this->appointmentData->date)
                    ->action('View Appointment', url('http://localhost:4200/staff-dashboard'))
                    // ->error() //shows the button in red clor
                    ->line('Thank you!');
                    // ->Subject('Upcoming Appointment');

                    // ->view('welcome');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // 'appointment_id'=>$this->appointmentDetails->id,
             'You have an Upcoming Appointment on'.$this->appointmentData->date
        ];
    }
}
