<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     */
    private $forgetPasswordToken;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var Collectivity
     */
    private $collectivity;

    public function __construct()
    {
        $this->id      = Uuid::uuid4();
        $this->roles   = [];
        $this->enabled = true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getFullName())) {
            return '';
        }

        if (\strlen($this->getFullName()) > 50) {
            return \substr($this->getFullName(), 0, 50) . '...';
        }

        return $this->getFullName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getForgetPasswordToken(): string
    {
        return $this->forgetPasswordToken;
    }

    /**
     * @param string|null $forgetPasswordToken
     */
    public function setForgetPasswordToken(?string $forgetPasswordToken): void
    {
        $this->forgetPasswordToken = $forgetPasswordToken;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isEnabledOrCollectivityActive(): bool
    {
        return $this->enabled && $this->getCollectivity()->isActive();
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return Collectivity|null
     */
    public function getCollectivity(): ?Collectivity
    {
        return $this->collectivity;
    }

    /**
     * @param Collectivity $collectivity
     */
    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }
}
