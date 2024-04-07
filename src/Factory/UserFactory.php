<?php

namespace App\Factory;

use DateTime;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 * @method        User|Proxy                     create(array|callable $attributes = [])
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[]                 all()
 * @method static User[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[]                 findBy(array $attributes)
 * @method static User[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public const VALID_TOKEN = 'this-is-a-valid-token-value';

    public const VALID_DOCTOR_TOKEN = 'this-is-a-valid-token-value-doctor';

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    public function withValidToken(): self
    {
        return $this->addState(
            [
                'access_token' => self::VALID_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );
    }

    public function secretary(): self
    {
        return self::new()->withValidToken();
    }

    //    public function patient(): self
    //    {
    //        $patient = PatientFactory::new()->create();
    //        return self::new()->withValidToken()->create(['patient' => $patient]);
    //    }
    public function doctor(): self
    {
        $userFactory = self::new()->withValidToken();
        DoctorFactory::new(['user' => $userFactory])->create();

        return $userFactory;
    }

    public function patient(): self
    {
        $userFactory = self::new()->withValidToken();
        PatientFactory::new(['user' => $userFactory])->create();

        return $userFactory;
    }

    public function admin(): self
    {
        return self::new(['email' => 'admin@admin.com'])->withValidToken();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => self::faker()->word(),
            'roles' => [],
            'access_token' => bin2hex(random_bytes(32)),
            'token_expiration' => self::faker()->dateTimeBetween('+30 day', '+60 day'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this->afterInstantiate(function (User $user): void {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
