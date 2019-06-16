<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniversRepository")
 */
class Univers
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
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Scenario", mappedBy="univers", orphanRemoval=true)
     */
    private $scenarios;

    public function __construct()
    {
        $this->scenarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Scenario[]
     */
    public function getScenarios(): Collection
    {
        return $this->scenarios;
    }

    public function addScenario(Scenario $scenario): self
    {
        if (!$this->scenarios->contains($scenario)) {
            $this->scenarios[] = $scenario;
            $scenario->setUnivers($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): self
    {
        if ($this->scenarios->contains($scenario)) {
            $this->scenarios->removeElement($scenario);
            // set the owning side to null (unless already changed)
            if ($scenario->getUnivers() === $this) {
                $scenario->setUnivers(null);
            }
        }

        return $this;
    }
}
