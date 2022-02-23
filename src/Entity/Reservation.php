<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity
 */
class Reservation
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
     * @var int|null
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=true)
     */
    private $idCustomer;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_room", type="integer", nullable=true)
     */
    private $idRoom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="arrival_date", type="date", nullable=true)
     */
    private $arrivalDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="reverser_date", type="date", nullable=true)
     */
    private $reverserDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCustomer(): ?int
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(?int $idCustomer): self
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    public function getIdRoom(): ?int
    {
        return $this->idRoom;
    }

    public function setIdRoom(?int $idRoom): self
    {
        $this->idRoom = $idRoom;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(?\DateTimeInterface $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getReverserDate(): ?\DateTimeInterface
    {
        return $this->reverserDate;
    }

    public function setReverserDate(?\DateTimeInterface $reverserDate): self
    {
        $this->reverserDate = $reverserDate;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(?int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }


}
