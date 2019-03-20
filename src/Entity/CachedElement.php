<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Repository\CachedElementMetadataRepository;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CachedElementRepository")
 */
class CachedElement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $dockerName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var Datetime
     */
    private $dateUpdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var Datetime
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="integer", nullable=false, unique=true)
     *
     * @var int
     */
    private $dockerPort;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="serviceId", referencedColumnName="id")
     *
     * @var Service
     */
    private $serviceId ;

    /**
     * @ORM\OneToMany(targetEntity="CachedElementMetadata", mappedBy="CachedElement")
     *
     * @var array[CachedElementMetadata]
     */
    private $metadatas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDockerName(): ?string
    {
        return $this->dockerName;
    }

    public function setDockerName(string $dockerName): self
    {
        $this->dockerName = $dockerName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(?\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getService(): Service
    {
        return $this->serviceId;
    }

    public function setService(Service $service): self
    {
        $this->serviceId = $service->getId();

        return $this;
    }

    /**
     * @return int
     */
    public function getDockerPort(): int
    {
        return $this->dockerPort;
    }

    /**
     * @param int $dockerPort
     *
     * @return self
     */
    public function setDockerPort(?int $dockerPort)
    {
        $this->dockerPort = (int) $dockerPort;

        return $this;
    }
}
