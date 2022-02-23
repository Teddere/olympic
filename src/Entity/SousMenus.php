<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SousMenus
 *
 * @ORM\Table(name="sous_menus")
 * @ORM\Entity
 */
class SousMenus
{
    /**
    * @ORM\ManyToOne(targetEntity="Menu")
    * @ORM\JoinColumn(name="id_menu", referencedColumnName="id")
    */
    private $menu;
    public function getMenu(): ?Menu
    {
    return $this->menu;
    }
    public function setMenu(?Menu $menu): self
    {
    $this->menu = $menu;

    return $this;
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_menu", type="integer", nullable=false)
     */
    private $idMenu;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link_name", type="string", length=20, nullable=true)
     */
    private $linkName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMenu(): ?int
    {
        return $this->idMenu;
    }

    public function setIdMenu(int $idMenu): self
    {
        $this->idMenu = $idMenu;

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

    public function getLinkName(): ?string
    {
        return $this->linkName;
    }

    public function setLinkName(?string $linkName): self
    {
        $this->linkName = $linkName;

        return $this;
    }


}
