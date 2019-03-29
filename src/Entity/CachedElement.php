<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CachedElementRepository")
 */
class CachedElement implements \JsonSerializable
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
    private $serviceId;

    /**
     * @ORM\OneToMany(targetEntity="CachedElementMetadata", mappedBy="CachedElement")
     *
     * @var array[CachedElementMetadata]
     */
    private $metadatas;

    /**
     * @return mixed
     */
    public function getId():  ? int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDockerName() :  ? string
    {
        return $this->dockerName;
    }

    /**
     * @param string $dockerName
     * @return mixed
     */
    public function setDockerName(string $dockerName) : self
    {
        $this->dockerName = $dockerName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName():  ? string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateUpdate():  ? \DateTimeInterface
    {
        return $this->dateUpdate;
    }

    /**
     * @param \DateTimeInterface $dateUpdate
     * @return mixed
     */
    public function setDateUpdate( ? \DateTimeInterface $dateUpdate) : self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated() :  ? \DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTimeInterface $dateCreated
     * @return mixed
     */
    public function setDateCreated( ? \DateTimeInterface $dateCreated) : self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return int
     */
    public function getService() : int
    {
        return $this->serviceId;
    }

    /**
     * @param Service $service
     * @return mixed
     */
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
     * @param  int    $dockerPort
     * @return self
     */
    public function setDockerPort( ? int $dockerPort)
    {
        $this->dockerPort = (int) $dockerPort;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id'           => $this->getId(),
            'docker_name'  => $this->getDockerName(),
            'name'         => $this->getName(),
            'date_update'  => $this->getDateUpdate(),
            'date_created' => $this->getDateCreated(),
            'docker_port'  => $this->getDockerPort(),
            // 'service'   => $this->getService(),
            // 'metadatas'    => $this->getMetadatas(),
        ];
    }
}
