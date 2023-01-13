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

    #[Route('/posts', name: 'posts')]
    public function index(Request $request): JsonResponse
    {
        return $this->json([
            'data' => $this->postRepository->findAll(),
            'tenant' => $request->attributes->get('tenant'),
        ]);
    }
}
