<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entry
 *
 * @ApiResource(
 *  collectionOperations={
 *      "get-tree"={"route_name"="entry_get_tree", "summary"="Get tree"},
 *      "search-code"={
 *          "route_name"="entry_search_code", "summary"="Search by code start",
 *          "swagger_context" = {
 *              "parameters" = {
 *                  {
 *                      "name" = "codeTerm",
 *                      "in" = "query",
 *                      "description" = "Code starts with",
 *                      "type" : "string",
 *                  },
 *              }
 *          }
 *      },
 *      "search-name"={
 *          "route_name"="entry_search_name", "summary"="Search by name or part",
 *          "swagger_context" = {
 *              "parameters" = {
 *                  {
 *                      "name" = "nameTerm",
 *                      "in" = "query",
 *                      "description" = "Name or part",
 *                      "type" : "string",
 *                  },
 *              }
 *          }
 *      },
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
 *  itemOperations={
 *  },
 *  attributes={
 *      "order"={"globalId": "ASC"},
 *      "pagination_enabled"=false,
 *  }
 * )
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Entity(repositoryClass="App\Repository\EntryRepository")
 */
class Entry
{
    /**
     * @var int global_id
     *
     * @ORM\Id
     * @ORM\Column(name="global_id", type="integer")
     * @Gedmo\TreePathSource
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
     * @Gedmo\TreePath
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Entry", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="global_id", onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=true)
     */
    private $lvl;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="parent")
     */
    private $children;

    /**
     * @return int
     */
    public function getGlobalId(): int
    {
        return $this->globalId;
    }

    /**
     * @param int $globalId
     * @return Entry
     */
    public function setGlobalId(int $globalId): Entry
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
     * @return Entry
     */
    public function setKod(string $kod): Entry
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
     * @return Entry
     */
    public function setNomdescr(?string $nomdescr): Entry
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
     * @return Entry
     */
    public function setIdx(string $idx): Entry
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
     * @return Entry
     */
    public function setRazdel(string $razdel): Entry
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
     * @return Entry
     */
    public function setName(string $name): Entry
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     * @return Entry
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Entry
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @param mixed $lvl
     * @return Entry
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     * @return Entry
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

}
