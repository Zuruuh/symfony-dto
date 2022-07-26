<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\DTO;
use App\DTO\User\LoginDTO;
use App\DTO\User\RegisterDTO;
use App\DTO\User\SessionDTO;
use App\Entity\User;
use App\Traits\SerializeDTOTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    use SerializeDTOTrait;

    #[Route('/register')]
    public function registerAction(#[DTO(RegisterDTO::class)] User $registrationData): Response
    {
        // $this->getDoctrine()->persist($registrationData);
        // $this->getDoctrine()->flush();

        return $this->returnDTO(SessionDTO::class, $registrationData);
    }

    #[Route('/login')]
    public function loginAction(LoginDTO $loginData): Response
    {
        if ($loginData->password === 'password') {
            // Whatever
        }

        return new Response();
    }
}
