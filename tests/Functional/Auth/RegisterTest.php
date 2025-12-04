<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Tests\Functional\DatabaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RegisterTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->purgeUserTable();
    }

    private function purgeUserTable(): void
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $platform = $conn->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $conn->executeStatement('DELETE FROM user;');
            $conn->executeStatement('DELETE FROM sqlite_sequence WHERE name="user";');
        } else {
            $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
            $conn->executeStatement('TRUNCATE TABLE user;');
            $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    #[DataProvider('provideInvalidFormData')]
    public function testRegistrationSucceeds(): void
    {
        $formData = self::getFormData();
        $expectedEmail = $formData['register']['email'];
        $expectedUsername = $formData['register']['username'];

        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', $formData);

        self::assertResponseRedirects('/auth/login');

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy([
            'email' => $expectedEmail,
        ]);

        self::assertNotNull($user);
        self::assertSame($expectedUsername, $user->getUsername());
    }

    #[DataProvider('provideInvalidFormData')]
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        // Préparer les données existantes pour les tests d'unicité
        if (
            ($formData['register[username]'] ?? null) === 'user+1'
            || ($formData['register[email]'] ?? null) === 'user+1@email.com'
        ) {
            $em = $this->getEntityManager();
            $existing = new User();
            $existing->setUsername('user+1');
            $existing->setEmail('user+1@email.com');
            $existing->setPlainPassword('Password123!');
            $em->persist($existing);
            $em->flush();
        }

        $this->get('/auth/register');

        $crawler = $this->client->getCrawler();
        $form = $crawler->filter('form[name="register"]')->form();
        $form->setValues($formData);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(422);
    }

    public static function provideInvalidFormData(): iterable
    {
        yield 'empty username' => [
            [
                'register[username]' => '',
                'register[email]' => 'user@email.com',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];

        yield 'non unique username' => [
            [
                'register[username]' => 'user+1',
                'register[email]' => 'user@email.com',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];

        yield 'too long username' => [
            [
                'register[username]' => 'Lorem ipsum dolor sit amet orci aliquam',
                'register[email]' => 'user@email.com',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];

        yield 'empty email' => [
            [
                'register[username]' => 'username',
                'register[email]' => '',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];

        yield 'non unique email' => [
            [
                'register[username]' => 'username',
                'register[email]' => 'user+1@email.com',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];

        yield 'invalid email' => [
            [
                'register[username]' => 'username',
                'register[email]' => 'fail',
                'register[plainPassword]' => 'SuperPassword123!',
            ],
        ];
    }

    public static function getFormData(array $overrideData = []): array
    {
        $data = [
            'register' => [
                'username' => 'username',
                'email' => 'user@email.com',
                'plainPassword' => 'SuperPassword123!',
            ],
        ];

        foreach ($overrideData as $key => $value) {
            if (preg_match('/register\[(.+)]/', $key, $m)) {
                $field = $m[1];
                $data['register'][$field] = $value;
            }
        }

        return $data;
    }
}
