<?php


namespace App\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct( $mailer){
        $this->mailer= $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'OnUserRegister'
        ];
    }
        public function OnUserRegister(UserRegisterEvent $event){
        }

}