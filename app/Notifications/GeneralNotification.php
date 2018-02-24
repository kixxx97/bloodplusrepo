<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\Gcm\GcmChannel;
use NotificationChannels\Gcm\GcmMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class GeneralNotification extends Notification
{
    use Queueable;

    private $message;
    private $user;
    private $class;
    private $title;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($class,$user,$message,$title)
    {
        $this->class = $class;
        $this->user = $user;
        $this->message = $message;
        $this->title = $title;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast',GcmChannel::class];
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
    public function toGcm($notifiable)
    {
        return GcmMessage::create()
            ->title('BloodPlus Notification')
            ->message($this->message)
            ->data('user',$this->user)
            ->data('saying',$this->message)
            ->data('class',$this->class);
    }

    public function toBroadcast($notifiable)
    {
        // dd($notifiable->id);
        return new BroadcastMessage([
            'data' => [
            'class' => $this->class,
            'user' => $this->user,
            'message' => $this->message
            ]
        ]);
    }
}
