<?php


namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        if($user instanceof User)
        {
            $data['data'] = array(
                'roles' => $user->getRoles(),
                'id' => $user->getId()
            );
        }
        $event->setData($data);
    }
}
