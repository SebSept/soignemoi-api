<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

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
use Symfony\Component\Serializer\SerializerInterface;

class GetTokenController extends AbstractController
{
    #[Route('/token', name: 'app_get_token', methods: ['POST'])]
    public function index(
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer): Response
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

        // serialisation maison pour applatir l'id patient/docteur sur un seul id
        $json = $serializer->serialize($user, 'json', [
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            'groups' => ['user:token'],
        ]);

        /** @var array{patient:array{id:int|null}, doctor:array{id:int|null},id: int|null, role:string} $rights */
        $rights = json_decode($json, true);
        // applatissement de ['patient']['id'] ou ['doctor']['id'] sur ['id']
        $rights['id'] = match (true) {
            isset($rights['patient']['id']) => $rights['patient']['id'],
            isset($rights['doctor']['id']) => $rights['doctor']['id'],
            default => null
        };
        unset($rights['patient'], $rights['doctor']);

        return new JsonResponse($rights, Response::HTTP_OK, json: false);
    }
}
