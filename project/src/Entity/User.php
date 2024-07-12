<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', 'Ce mail est déjà utilisé.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    private string $avatar = '0';

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank()]
    private array $roles = ['ROLE_USER'];

    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    private string $password = '0';

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(name: 'github_id', type: 'string', length: 255, nullable: true)]
    private ?string $githubId = null;

    #[ORM\Column(name: 'github_access_token', type: 'string', length: 255, nullable: true)]
    private ?string $githubAccessToken = null;

    #[ORM\Column(name: 'google_id', type: 'string', length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(name: 'google_access_token', type: 'string', length: 255, nullable: true)]
    private ?string $googleAccessToken = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->avatar = 'https://api.dicebear.com/8.x/fun-emoji/svg?seed=' . rand(0, 100);
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles = ['ROLE_USER'];
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function eraseCredentials():void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier():string
    {
        return $this->email;
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function setGithubId(?string $githubId): self
    {
        $this->githubId = $githubId;
        return $this;
    }

    public function getGithubAccessToken(): ?string
    {
        return $this->githubAccessToken;
    }

    public function setGithubAccessToken(?string $githubAccessToken): self
    {
        $this->githubAccessToken = $githubAccessToken;
        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;
        return $this;
    }

    public function getGoogleAccessToken(): ?string
    {
        return $this->googleAccessToken;
    }

    public function setGoogleAccessToken(?string $googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;
        return $this;
    }
}
