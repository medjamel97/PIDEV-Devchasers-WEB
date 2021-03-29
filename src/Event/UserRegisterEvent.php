<?php


namespace App\Event;
use App\Entity\Utilisateur;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserRegisterEvent extends EventDispatcher
{
    const NAME='user.register';
    /**
     * @var Utilisateur
     */
    private $RegisteredUser;

    public function __construct(Utilisateur $RegisteredUser)
    {
    $this->RegisteredUser=$RegisteredUser;
    }

    /**
     * @return Utilisateur
     */
    public function getRegisteredUser(): Utilisateur
    {
        return $this->RegisteredUser;
    }

}