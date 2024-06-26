<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        // "Bearer " est déjà retiré par Symfony
        // $accessToken = str_replace('Bearer ', '', $accessToken);
        /** @var User|null $user */
        $user = $this->userRepository->findOneBy(['accessToken' => $accessToken]);
        if (is_null($user)) {
            throw new BadCredentialsException('Invalid credentials. token not found.');
        }

        if (!$user->isTokenValid()) {
            throw new BadCredentialsException('Invalid credentials.  token expired.');
        }

        return new UserBadge($user->getEmail());
    }
}
