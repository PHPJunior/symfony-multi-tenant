<?php

namespace App\Central\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Central\Repository\TenantRepository;


#[ORM\Entity(
    repositoryClass: TenantRepository::class,
    readOnly: false,
)]
#[ORM\Table(name: 'tenants')]
class Tenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'subdomain', type: 'string', length: 255, unique: true)]
    public string $subDomain;

    #[ORM\Column(type: 'json')]
    public array $data;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $updatedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Tenant
     */
    public function setId(int $id): Tenant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubDomain(): string
    {
        return $this->subDomain;
    }

    /**
     * @param string $subDomain
     * @return Tenant
     */
    public function setSubDomain(string $subDomain): Tenant
    {
        $this->subDomain = $subDomain;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Tenant
     */
    public function setData(array $data): Tenant
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return Tenant
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): Tenant
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Tenant
     */
    public function setUpdatedAt(\DateTime $updatedAt): Tenant
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDbname()
    {
        return $this->data['dbname'] ?? '';
    }
}
