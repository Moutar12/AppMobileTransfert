<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiResource(
  *  collectionOperations={
 * "add_profil":{
 *        "method":"POST",
 *        "path":"/admin/profils",
 *        "normalization_context"={"groups"="profil:write"},
 *        "access_control"="(is_granted('ROLE_adminSystem'))",
 *        "access_control_message"="Vous n'étes pas autorisé à cette ressource"
 *
 *     },
 *     "get_profil":{
 *        "method":"GET",
 *        "path":"/admin/profils",
 *        "normalization_context"={"groups"="profil:read"},
 *        "access_control"="(is_granted('ROLE_adminSystem'))",
 *        "access_control_message"="Vous n'étes pas autorisé à cette ressource"
 *
 *     },
 *     }
  *   )
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *@Groups({"profil:write","profil:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *@Groups({"profil:write","profil:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *@Groups({"profil:write"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     */
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }
}
