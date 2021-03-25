<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 *  collectionOperations={
  *  "add_compte":{
  *        "method":"POST",
  *        "path":"/admin/compte",
  *        "normalization_context"={"groups"="compte:write"},
  *        "access_control"="(is_granted('ROLE_adminSystem'))",
  *        "access_control_message"="Vous n'étes pas autorisé à cette ressource"
  *     },
  *   "get_compte":{
 *              "method":"GET",
   *              "path":"/admin/compte",
   *              "normalization_context"={"groups"="compte:read"},
   *              "access_control"="(is_granted('ROLE_adminSystem') || is_granted('ROLE_caissier') )",
   *              "access_control_message"="Vous n'étes pas autorisé à cette Ressource",
   *     }
  *     },
 *     itemOperations={
 *          "getComptebyId"={
 *               "path"="/admin/compte/{id}" ,
 *               "method"="GET" ,
 *                "security_post_denormalize"="is_granted('ROLE_adminSystem') || is_granted('ROLE_adminAgence') || is_granted('ROLE_userAgence') || is_granted('ROLE_caissier')" ,
 *                "security_message"="Only admin system can see a a count"
 *           },
 *           "bloquerCompte"={
 *               "path"="/admin/compte/{id}" ,
 *               "method"="DELETE" ,
 *                "security_post_denormalize"="is_granted('ROLE_adminSystem')" ,
 *                "security_message"="Only admin system can block a a count"
 *
 *          }
 *    }
   *   )
  */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte:write","compte:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"compte:write","compte:read","agence:write"})
     */
    private $numCompte;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comptes")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="compte")
     */
    private $depots;


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"compte:write","compte:read","agence:write"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="compteEnvoie")
     */
    private $transactions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity=Agence::class, cascade={"persist", "remove"})
     */
    private $agence;



    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->caissiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

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

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }



    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setCompteEnvoie($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCompteEnvoie() === $this) {
                $transaction->setCompteEnvoie(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }


}
