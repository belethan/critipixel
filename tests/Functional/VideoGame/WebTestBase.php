<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class WebTestBase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = self::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function get(string $url): void
    {
        $this->client->request('GET', $url);
    }

    protected function post(string $url, array $params): void
    {
        $this->client->request('POST', $url, $params);
    }

    /**
     * Simule la connexion d’un utilisateur (fixtures obligatoires).
     */
    protected function login(string $email = 'user+10@example.com'): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        self::assertNotNull($user, "Aucun user trouvé avec l'email : $email");

        $this->client->loginUser($user);
    }

    /**
     * Soumet un formulaire contenant un bouton avec un label donné.
     */
    protected function submit(string $buttonText, array $formData = []): void
    {
        $crawler = $this->client->getCrawler();

        $button = $crawler->selectButton($buttonText);
        self::assertGreaterThan(
            0,
            $button->count(),
            "Bouton '$buttonText' introuvable dans la page"
        );

        $form = $button->form();
        $this->client->submit($form, $formData);
    }
}
