<?php

// src/EventListener/JWTCreatedListener.php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $event->getUser();

        if ($user instanceof User) {
            // Ajoutez les informations de l'utilisateur
            $payload['roles'] = $user->getRoles();
            $payload['id'] = $user->getId();
            $payload['email'] = $user->getEmail();
            $payload['avatar'] = $user->getAvatar();

            // Ajoutez ici les informations de l'établissement
            if ($user->getInstitut()) {
                $payload['etablissement'] = [
                    'nom' => $user->getInstitut()->getName(),
                    'adresse' => $user->getInstitut()->getAdresse(),
                    'type' => $user->getInstitut()->getType()
                ];
            }

            // Ajoutez ici les informations de l'ambassade
            if ($user->getDemandeur()) {
                $payload['ambassade'] = [
                    'nom' => $user->getAmbassade()->getNom(),
                    'adresse' => $user->getAmbassade()->getAdresse(),
                ];
            }
        }

        $event->setData($payload);
    }
}
