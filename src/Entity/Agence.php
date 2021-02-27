<?php

namespace App\Entity;

use App\Repository\AgenceRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 * @ApiResource(
*  collectionOperations={
 *     "add_agence":{
 *        "method":"POST",
 *        "path":"/admin/agence",
 *        "normalization_context"={"groups"="agence:write"},
 *        "access_control"="(is_granted('ROLE_adminSystem'))",
 *        "access_control_message"="Vous n'étes pas autorisé à cette ressource"
 *     },
*     }
*   )
 */
class Agence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"agence:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"agence:write"})
     */
    private $nomAgence;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"agence:write"})
     */
    private $adressAgence;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agence")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, cascade={"persist", "remove"})
     * *  @Groups({"agence:write"})
     * @ApiSubresource
     */
    private $compte;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdressAgence(): ?string
    {
        return $this->adressAgence;
    }

    public function setAdressAgence(string $adressAgence): self
    {
        $this->adressAgence = $adressAgence;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setAgence($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgence() === $this) {
                $user->setAgence(null);
            }
        }

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
