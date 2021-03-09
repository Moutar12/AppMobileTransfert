<?php

namespace App\Entity;

use App\Repository\TransactionsRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 * @ApiResource (
 *      collectionOperations={
 *          "transfertClient"={
 *                "route_name"="transfertClient" ,
 *                "method"="POST",
 *                   "deserialize"= false
 *           } ,
 *     "reucuperTransaction"={
 *                "route_name"="reucuperTransaction" ,
 *                "method"="PUT",
 *                   "deserialize"= false
 *           },
 *           "getAllTransaction"={
 *                  "path"="/transactions" ,
 *                   "method"="GET" ,
 *                   "normalization_context"={"groups"={"transactions:read"}} ,
 *          },
 *     "getTransactionByCode"={
 *                "route_name"="getTransactionByCode" ,
 *                "method"="GET",
 *                   "deserialize"= false
 *           },
 *     },
 *
 * )
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *@Groups({"transactions:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=255)
     * @Groups({"transactions:read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"transactions:read"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date")
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateAnnulation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $tTc;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $fraisSystem;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $fraisEnvoie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactions:read"})
     */
    private $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"transactions:read"})
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"transactions:read"})
     */
    private $userRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    private $clientDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    private $clientRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     */
    private $compteEnvoie;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     */
    private $compteRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;




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

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(?\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getTTc(): ?string
    {
        return $this->tTc;
    }

    public function setTTc(string $tTc): self
    {
        $this->tTc = $tTc;

        return $this;
    }

    public function getFraisEtat(): ?string
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(string $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSystem(): ?string
    {
        return $this->fraisSystem;
    }

    public function setFraisSystem(string $fraisSystem): self
    {
        $this->fraisSystem = $fraisSystem;

        return $this;
    }

    public function getFraisEnvoie(): ?string
    {
        return $this->fraisEnvoie;
    }

    public function setFraisEnvoie(string $fraisEnvoie): self
    {
        $this->fraisEnvoie = $fraisEnvoie;

        return $this;
    }

    public function getFraisRetrait(): ?string
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(string $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getClientDepot(): ?Client
    {
        return $this->clientDepot;
    }

    public function setClientDepot(?Client $clientDepot): self
    {
        $this->clientDepot = $clientDepot;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->clientRetrait;
    }

    public function setClientRetrait(?Client $clientRetrait): self
    {
        $this->clientRetrait = $clientRetrait;

        return $this;
    }

    public function getCompteEnvoie(): ?Compte
    {
        return $this->compteEnvoie;
    }

    public function setCompteEnvoie(?Compte $compteEnvoie): self
    {
        $this->compteEnvoie = $compteEnvoie;

        return $this;
    }

    public function getCompteRetrait(): ?Compte
    {
        return $this->compteRetrait;
    }

    public function setCompteRetrait(?Compte $compteRetrait): self
    {
        $this->compteRetrait = $compteRetrait;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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




}
