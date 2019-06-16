<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeJoueurRepository")
 */
class TypeJoueur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $race;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PersonnageJoueur", mappedBy="race")
     */
    private $Pj;

    public function __construct()
    {
        $this->Pj = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): self
    {
        $this->race = $race;

        return $this;
    }

    /**
     * @return Collection|PersonnageJoueur[]
     */
    public function getPj(): Collection
    {
        return $this->Pj;
    }

    public function addPj(PersonnageJoueur $pj): self
    {
        if (!$this->Pj->contains($pj)) {
            $this->Pj[] = $pj;
            $pj->setRace($this);
        }

        return $this;
    }

    public function removePj(PersonnageJoueur $pj): self
    {
        if ($this->Pj->contains($pj)) {
            $this->Pj->removeElement($pj);
            // set the owning side to null (unless already changed)
            if ($pj->getRace() === $this) {
                $pj->setRace(null);
            }
        }

        return $this;
    }
}
