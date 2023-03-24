<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendVisitorEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $visitingDetails;

    /**
     * SendInvitationToVisitors constructor.
     * @param $visitingDetails
     */
    public function __construct($visitingDetails)
    {
        $this->visitingDetails = $visitingDetails;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $array = ['database'];
        if (setting('twilio_disabled') != 0 &&
            !blank(setting('twilio_from')) &&
            !blank(setting('twilio_auth_token')) &&
            !blank(setting('twilio_account_sid'))
        ) {
            array_push($array, TwilioChannel::class);
        }

        if (setting('notifications_email') != false &&
            !blank(setting('mail_host')) &&
            !blank(setting('mail_username')) &&
            !blank(setting('mail_password')) &&
            !blank(setting('mail_port')) &&
            !blank(setting('mail_from_name')) &&
            !blank(setting('mail_from_address'))
        ) {
            array_push($array, 'mail');
        }

        return $array;
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
            ->subject("Visitor Confirmetion#" .$this->visitingDetails->reg_no)
            ->greeting('Hello!')
            ->line('New Visitor Register please Approved your visitor')
            
            ->line('Visitor Name: '. $this->visitingDetails->visitor->name . ' Email: ' .$this->visitingDetails->visitor->email . ' phone: ' .$this->visitingDetails->visitor->phone.' Status: ' .$this->visitingDetails->visitor->status)
            ->line('Thank you for using our application! '.setting('site_name'));
    }

    /**
     * @param $notifiable
     * @return \NotificationChannels\Twilio\TwilioMessage|TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content(setting('notify_templates') .'New Visitor Register'. 'Visitor Name: '. $this->visitingDetails->visitor->name . ' Email: ' .$this->visitingDetails->visitor->email .' Phone: ' .$this->visitingDetails->visitor->phone);
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
}
