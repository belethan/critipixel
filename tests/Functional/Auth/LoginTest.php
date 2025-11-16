<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class LoginTest extends FunctionalTestCase
{
    public function testThatLoginShouldSucceeded(): void
    {
        // 1. On affiche le formulaire de connexion
        $crawler = $this->get('/auth/login');

        // 2. On soumet le formulaire
        $crawler = $this->client->submitForm('Se connecter', [
            'email' => 'user+1@email.com',
            'password' => 'password'
        ]);


        // 3. Une connexion valide redirige toujours -> on vérifie la redirection
        self::assertTrue($this->client->getResponse()->isRedirect());



        // 4. On suit la redirection
        $this->client->followRedirect();

        // 5. On récupère le token de sécurité (pour vérifier la connexion)
        $token = $this->client->getContainer()->get('security.token_storage')->getToken();

        // ✔️ Le token ne doit pas être null après une connexion réussie
        self::assertNotNull($token, 'Le token ne doit pas être null après connexion');

        // ✔️ En Symfony 7.3, on vérifie la connexion via getUser()
        self::assertNotNull($token->getUser(), 'Un utilisateur doit être associé au token');
        self::assertNotSame('anon.', $token->getUser(), 'L\'utilisateur ne doit pas être un anonyme');

        // 6. On teste la déconnexion
        $this->get('/auth/logout');

        // 7. Après logout → le token doit être remis à null
        $tokenAfterLogout = $this->client->getContainer()->get('security.token_storage')->getToken();
        self::assertNull($tokenAfterLogout, 'Le token doit être null après déconnexion');
    }



    public function testThatLoginShouldFailed(): void
    {
        // 1. Charger la page de login
        $this->client->request('GET', '/auth/login');

        // 2. Soumettre un mauvais mot de passe
        $this->client->submitForm('Se connecter', [
            'email' => 'user1@email.com',
            'password' => 'badpassword'
        ]);

        // 3. Suivre la redirection (retour sur /auth/login)
        $this->client->followRedirect();

        // 4. Récupérer le token
        $token = $this->client->getContainer()->get('security.token_storage')->getToken();

        // 5. Assert : l'utilisateur ne doit PAS être connecté
        self::assertTrue(
            ! $token || $token->getUser() === 'anon.',
            'L’utilisateur ne doit pas être authentifié avec un mauvais mot de passe'
        );
    }

}
