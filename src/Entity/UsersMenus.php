<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersMenus
 *
 * @ORM\Table(name="users_menus")
 * @ORM\Entity
 */
class UsersMenus
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=30, nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_menu", type="integer", nullable=true)
     */
    private $idMenu;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_users", type="integer", nullable=true)
     */
    private $idUsers;

    /**
     * @var string|null
     *
     * @ORM\Column(name="id_sous_menus", type="string", length=20, nullable=true)
     */
    private $idSousMenus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIdMenu(): ?int
    {
        return $this->idMenu;
    }

    public function setIdMenu(?int $idMenu): self
    {
        $this->idMenu = $idMenu;

        return $this;
    }

    public function getIdUsers(): ?int
    {
        return $this->idUsers;
    }

    public function setIdUsers(?int $idUsers): self
    {
        $this->idUsers = $idUsers;

        return $this;
    }

    public function getIdSousMenus(): ?string
    {
        return $this->idSousMenus;
    }

    public function setIdSousMenus(?string $idSousMenus): self
    {
        $this->idSousMenus = $idSousMenus;

        return $this;
    }


}
