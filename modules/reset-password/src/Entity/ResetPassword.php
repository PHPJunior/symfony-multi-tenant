<?php

namespace Module\ResetPassword\Entity;

use Doctrine\ORM\Mapping as ORM;
use Module\ResetPassword\Repository\ResetPasswordRepository;

#[ORM\Entity(
    repositoryClass: ResetPasswordRepository::class,
    readOnly: false,
)]
#[ORM\Table(name: 'reset_password')]
class ResetPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    public int $userId;

    #[ORM\Column(name: 'hashed_token', type: 'string')]
    public string $hashedToken;

    #[ORM\Column(name: 'requested_at', type: 'datetime')]
    public \DateTimeInterface $requestedAt;

    #[ORM\Column(name: 'expires_at', type: 'datetime')]
    public \DateTimeInterface $expiresAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ResetPassword
     */
    public function setId(int $id): ResetPassword
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return ResetPassword
     */
    public function setUserId(int $userId): ResetPassword
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    /**
     * @param string $hashedToken
     * @return ResetPassword
     */
    public function setHashedToken(string $hashedToken): ResetPassword
    {
        $this->hashedToken = $hashedToken;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }

    /**
     * @param \DateTimeInterface $requestedAt
     * @return ResetPassword
     */
    public function setRequestedAt(\DateTimeInterface $requestedAt): ResetPassword
    {
        $this->requestedAt = $requestedAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTimeInterface $expiresAt
     * @return ResetPassword
     */
    public function setExpiresAt(\DateTimeInterface $expiresAt): ResetPassword
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }
}
