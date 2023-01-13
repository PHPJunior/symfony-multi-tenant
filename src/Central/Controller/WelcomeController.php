<?php

namespace App\Central\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    #[Route('/welcome', name: 'Central_welcome')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to Multi Tenant App!',
        ]);
    }
}
