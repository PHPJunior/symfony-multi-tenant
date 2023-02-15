<?php

namespace App\Central\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    #[Route('/api/welcome', name: 'welcome')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to Multi Tenant App!',
            'logged_in_user' => [
                'id' => $this->getUser()->getId(),
                'email' => $this->getUser()->getEmail(),
                'roles' => $this->getUser()->getRoles(),
            ],
        ]);
    }
}
