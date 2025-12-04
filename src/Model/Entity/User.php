<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Doctrine\EntityListener\UserListener;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[ORM\EntityListeners([UserListener::class])]

#[UniqueEntity(
    fields: ['email'],
    message: 'Cet email est déjà utilisé.'
)]
#[UniqueEntity(
    fields: ['username'],
    message: 'Ce pseudo est déjà utilisé.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Pseudo obligatoire')]
    #[Assert\Length(
        max: 30,
        maxMessage: 'Le pseudo ne peut pas dépasser 30 caractères'
    )]
    #[ORM\Column(length: 30, unique: true)]
    private string $username;

    #[Assert\NotBlank(message: 'Email obligatoire')]
    #[Assert\Email(message: 'Email invalide')]
    #[ORM\Column(unique: true)]
    private string $email;

    #[ORM\Column(length: 60)]
    private string $password;

    private ?string $plainPassword = null;

    // ---------------------------------------------------
    // Getters / Setters
    // ---------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    // ---------------------------------------------------
    // Security
    // ---------------------------------------------------

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }
}
