<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EtlEntry - entry for ETL
 *
 * @ApiResource(
 *  collectionOperations={
 *    "load-xml"={
 *      "route_name"="entry_load_xml",
 *      "summary"="Load from XML request and bulk insert entries",
 *      "swagger_context"={
 *          "consumes"={"multipart/form-data"},
 *          "parameters"={
 *              {
 *                  "in"="formData",
 *                  "name"="file",
 *                  "type"="file",
 *                  "description"="The file to upload",
 *              },
 *          },
 *      },
 *    },
 *  },
 *  itemOperations={},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\EtlEntryRepository")
 */
class EtlEntry
{
    /**
     * @var int global_id
     *
     * @ORM\Id
     * @ORM\Column(name="global_id", type="integer")
     */
    private $globalId;

    /**
     * @var string Kod
     *
     * @ApiProperty(required=true)
     * @ORM\Column(name="Kod", type="string", nullable=true)
     * @Assert\NotBlank
     */
    private $kod = '';

    /**
     * @var null|string Nomdescr
     *
     * @ApiProperty
     * @ORM\Column(name="Nomdescr", type="string", length=4096, nullable=true)
     */
    private $nomdescr = null;

    /**
     * @var string Idx
     *
     * @ApiProperty(required=true)
     * @ORM\Column(name="Idx", type="string")
     * @Assert\NotBlank
     */
    private $idx = '';

    /**
     * @var string Razdel
     *
     * @ApiProperty(required=true)
     * @ORM\Column(name="Razdel", type="string")
     * @Assert\NotBlank
     */
    private $razdel = '';

    /**
     * @var string Name
     *
     * @ORM\Column(name="Name", type="string", length=1024)
     * @Assert\NotBlank
     */
    private $name = '';

    /**
     * @var int level
     * @ORM\Column(name="level", type="integer")
     * @Assert\NotBlank
     */
    private $level;

    /**
     * @return int
     */
    public function getGlobalId(): int
    {
        return $this->globalId;
    }

    /**
     * @param int $globalId
     * @return EtlEntry
     */
    public function setGlobalId(int $globalId): EtlEntry
    {
        $this->globalId = $globalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getKod(): string
    {
        return $this->kod;
    }

    /**
     * @param string $kod
     * @return EtlEntry
     */
    public function setKod(string $kod): EtlEntry
    {
        $this->kod = $kod;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getNomdescr(): ?string
    {
        return $this->nomdescr;
    }

    /**
     * @param null|string $nomdescr
     * @return EtlEntry
     */
    public function setNomdescr(?string $nomdescr): EtlEntry
    {
        $this->nomdescr = $nomdescr;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdx(): string
    {
        return $this->idx;
    }

    /**
     * @param string $idx
     * @return EtlEntry
     */
    public function setIdx(string $idx): EtlEntry
    {
        $this->idx = $idx;
        return $this;
    }

    /**
     * @return string
     */
    public function getRazdel(): string
    {
        return $this->razdel;
    }

    /**
     * @param string $razdel
     * @return EtlEntry
     */
    public function setRazdel(string $razdel): EtlEntry
    {
        $this->razdel = $razdel;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EtlEntry
     */
    public function setName(string $name): EtlEntry
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return EtlEntry
     */
    public function setLevel(int $level): EtlEntry
    {
        $this->level = $level;
        return $this;
    }


}
