<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CachedElement;
use App\Repository\CachedElementRepository;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CachedElementMetadataRepository")
 */
class CachedElementMetadata
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="CachedElement")
     * @ORM\JoinColumn(name="cachedElementID", referencedColumnName="id")
     * @var CachedElement
     */
    private $cachedElementID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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

    public function getCachedElementId(): ?CachedElement
    {
        return $this->cachedElementID;
    }

    public function setCachedElementId(int $cachedElementID): self
    {
        $this->cachedElementID = $cachedElementID;

        return $this;
    }
}
