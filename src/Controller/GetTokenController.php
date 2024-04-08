<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetTokenController extends AbstractController
{
    #[Route('/token', name: 'app_get_token', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {
        if (is_null($user)) {
            return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $user->generateToken();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $exception) {
            throw new RuntimeException('Token generation failed : '.$exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this->json($user, context: ['groups' => ['user:token']]);
    }
}
