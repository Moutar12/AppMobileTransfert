<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TarifRepository::class)
 */
class Tarif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneSuperieur;

    /**
     * @ORM\Column(type="integer")
     */
    private $bornInferieur;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisEnvoie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneSuperieur(): ?int
    {
        return $this->borneSuperieur;
    }

    public function setBorneSuperieur(int $borneSuperieur): self
    {
        $this->borneSuperieur = $borneSuperieur;

        return $this;
    }

    public function getBornInferieur(): ?int
    {
        return $this->bornInferieur;
    }

    public function setBornInferieur(int $bornInferieur): self
    {
        $this->bornInferieur = $bornInferieur;

        return $this;
    }

    public function getFraisEnvoie(): ?int
    {
        return $this->fraisEnvoie;
    }

    public function setFraisEnvoie(int $fraisEnvoie): self
    {
        $this->fraisEnvoie = $fraisEnvoie;

        return $this;
    }
}
