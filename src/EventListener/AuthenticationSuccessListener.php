<?php

// src/EventListener/AuthenticationSuccessListener.php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
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

        if ($user instanceof User) {
            $data['user'] = [
                'roles' => $user->getRoles(),
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
            ];

            // Ajoutez ici les informations de l'établissement
            if ($user->getDemandeur()) {
                $data['user']['demandeur'] = [
                    'id' => $user->getDemandeur()->getId(),
                    'name' => $user->getDemandeur()->getName(),
                    'adresse' => $user->getDemandeur()->getAdresse(),
                    'phone' => $user->getDemandeur()->getPhone(),
                    'email' => $user->getDemandeur()->getEmail(),
                    // 'code' => $user->getDemandeur()->getCode(),
                    'intitule' => $user->getDemandeur()->getIntitule(),
                    'dateNaissance' => $user->getDemandeur()->getDateNaissance(),
                    'lieuNaissance' => $user->getDemandeur()->getLieuNaissance(),
                    'profession' => $user->getDemandeur()->getProfession(),
                    'sexe' => $user->getDemandeur()->getSexe(),
                    'paysResidence' => $user->getDemandeur()->getPaysResidence(),
                ];
            }

            // Ajoutez ici les informations de l'ambassade
            if ($user->getInstitut()) {
                $data['user']['institut'] = [
                    'id' => $user->getInstitut()->getId(),
                    'name' => $user->getInstitut()->getName(),
                    'type' => $user->getInstitut()->getType(),
                    'adresse' => $user->getInstitut()->getAdresse(),
                    'phone' => $user->getInstitut()->getPhone(),
                    'email' => $user->getInstitut()->getEmail(),
                    // 'code' => $user->getInstitut()->getCode(),
                    'intitule' => $user->getInstitut()->getIntitule(),
                    'siteWeb' => $user->getInstitut()->getSiteWeb(),
                    'paysResidence' => $user->getInstitut()->getPaysResidence(),
                    'logo' => $user->getInstitut()->getLogo(),
                ];
            }
        }

        $event->setData($data);
    }
}
