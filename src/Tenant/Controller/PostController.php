<?php

namespace App\Tenant\Controller;

use App\Tenant\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    #[Route('/api/posts', name: 'posts')]
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser();
        return $this->json([
            'data' => $this->postRepository->findAll(),
            'tenant' => $request->attributes->get('tenant'),
            'logged_in_user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
}
