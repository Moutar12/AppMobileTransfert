<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 *@ApiResource(
 *  collectionOperations={
 *
 *     "caisseDepot"={
 *                "route_name"="caisseDepot" ,
 *                "access_control"="(is_granted('ROLE_caissier'))",
 *
 *           } ,
 *
 *     "get_depot":{
 *              "method":"GET",
 *              "path":"/admin/depot",
 *              "normalization_context"={"groups"="depot:read"},
 *              "access_control"="(is_granted('ROLE_adminSystem') or is_granted('ROLE_caissier'))",
 *              "access_control_message"="Vous n'étes pas autorisé à cette Ressource",
 *     }
 *     },
 *
 *   )
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *@Groups({"depot:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *@Groups({"depot:write","depot:read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date")
     * *@Groups({"depot:write","depot:read"})
     */
    private $dateDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots")
     *@Groups({"depot:write","depot:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     * *@Groups({"depot:write","depot:read"})
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
