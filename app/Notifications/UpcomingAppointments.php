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

    private $appointmentDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct($appointmentDetails)
    {
        $this->appointmentDetails = $appointmentDetails;
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
                    ->line('You have an upcoming appointment on 14th August 2024.')
                    ->action('View Appointment', url('http://localhost:4200/staff-dashboard'))
                    // ->error() //shows the button in red clor
                    ->line('Thank you!')
                    ->Subject('Upcoming Appointment');

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
            'message'=> 'details of upcoming appointment'
        ];
    }
}
