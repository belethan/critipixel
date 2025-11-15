<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RegisterTest extends FunctionalTestCase
{
    public function testThatRegistrationShouldSucceeded(): void
    {
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', self::getFormData());

        self::assertResponseRedirects('/auth/login');

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy([
            'email' => 'user@email.com'
        ]);

        self::assertNotNull($user);
        self::assertSame('username', $user->getUsername());
    }

    /**
     * @dataProvider provideInvalidFormData
     */
    #[DataProvider('provideInvalidFormData')]
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        // Préparer les données existantes pour les tests d'unicité
        if (
            ($formData['register[username]'] ?? null) === 'user+1' ||
            ($formData['register[email]'] ?? null) === 'user+1@email.com'
        ) {
            $existing = new User();
            $existing->setUsername('user+1');
            $existing->setEmail('user+1@email.com');
            $existing->setPlainPassword('Password123!');
            $em = $this->getEntityManager();
            $em->persist($existing);
            $em->flush();
        }

        // Charger la page
        $this->get('/auth/register');

        // Vérifier que les champs existent
        self::assertSelectorExists('input[name="register[username]"]');
        self::assertSelectorExists('input[name="register[email]"]');
        self::assertSelectorExists('input[name="register[plainPassword]"]');

        $crawler = $this->client->getCrawler();

        // Récupération correcte du formulaire
        $form = $crawler->filter('form[name="register"]')->form();

        // ✔ Injection des valeurs invalides
        $form->setValues($formData);

        // Soumission
        $this->client->submit($form);

        // ✔ Le controller renvoie bien 422 en cas d'erreur
        self::assertResponseStatusCodeSame(422);

    }


    //La fonction provideInvalidFormData est utilisée par PHPUNIT en lisant attribut DataProvider dans le test
    public static function provideInvalidFormData(): iterable
    {
       /* yield 'empty username' => self::getFormData(['register[username]' => '']);
        yield 'non unique username' => self::getFormData(['register[username]' => 'user+1']);
        yield 'too long username' => self::getFormData(['register[username]' => 'Lorem ipsum dolor sit amet orci aliquam']);
        yield 'empty email' => self::getFormData(['register[email]' => '']);
        yield 'non unique email' => self::getFormData(['register[email]' => 'user+1@email.com']);
        yield 'invalid email' => self::getFormData(['register[email]' => 'fail']);*/
        yield 'empty username' => [[
            'register' => [
                'username' => '',
                'email' => 'user@email.com',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];

        yield 'non unique username' => [[
            'register' => [
                'username' => 'user+1',
                'email' => 'user@email.com',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];

        yield 'too long username' => [[
            'register' => [
                'username' => 'Lorem ipsum dolor sit amet orci aliquam',
                'email' => 'user@email.com',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];

        yield 'empty email' => [[
            'register' => [
                'username' => 'username',
                'email' => '',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];

        yield 'non unique email' => [[
            'register' => [
                'username' => 'username',
                'email' => 'user+1@email.com',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];

        yield 'invalid email' => [[
            'register' => [
                'username' => 'username',
                'email' => 'fail',
                'plainPassword' => 'SuperPassword123!'
            ]
        ]];
    }

    public static function getFormData(array $overrideData = []): array
    {
        $data = [
            'register' => [
                'username' => 'username',
                'email' => 'user@email.com',
                'plainPassword' => 'SuperPassword123!',
            ]
        ];

        foreach ($overrideData as $key => $value) {
            // Exemple : "register[username]"
            if (preg_match('/register\[(.+)\]/', $key, $m)) {
                $field = $m[1];
                $data['register'][$field] = $value;
            }
        }

        return $data;
    }
}
