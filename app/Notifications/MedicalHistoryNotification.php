<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\Gcm\GcmChannel;
use NotificationChannels\Gcm\GcmMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class MedicalHistoryNotification extends Notification
{
    use Queueable;


    private $message;
    private $user;
    private $class;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($class,$user,$message)
    {
        $this->class = $class;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast',GcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'class' => $this->class,
            'user' => $this->user,
            'message' => $this->message
        ];
    }

    public function toGcm($notifiable)
    {
        return GcmMessage::create()
            ->title('BloodPlus Notification')
            ->message($this->message)
            ->data('user',$this->user)
            ->data('class',$this->class)
            ->data('saying',$this->message);

    }

    public function toBroadcast($notifiable)
    {
        // dd($notifiabl e->id);
        return new BroadcastMessage([
            'data' => [
            'class' => $this->class,
            'user' => $this->user,
            'message' => $this->message
            ]
        ]);
    }
}
