<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MaitreDuJeu", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mj;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PersonnageJoueur", inversedBy="sessions")
     */
    private $personnageJoueur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $scenario;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFin = null;

    public function __construct()
    {
        $this->personnageJoueur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }


    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getMj(): ?MaitreDuJeu
    {
        return $this->mj;
    }

    public function setMj(?MaitreDuJeu $mj): self
    {
        $this->mj = $mj;

        return $this;
    }

    /**
     * @return Collection|PersonnageJoueur[]
     */
    public function getPersonnageJoueur(): Collection
    {
        return $this->personnageJoueur;
    }

    public function addPersonnageJoueur(PersonnageJoueur $personnageJoueur): self
    {
        if (!$this->personnageJoueur->contains($personnageJoueur)) {
            $this->personnageJoueur[] = $personnageJoueur;
        }

        return $this;
    }

    public function removePersonnageJoueur(PersonnageJoueur $personnageJoueur): self
    {
        if ($this->personnageJoueur->contains($personnageJoueur)) {
            $this->personnageJoueur->removeElement($personnageJoueur);
        }

        return $this;
    }

    public function getScenario(): ?Scenario
    {
        return $this->scenario;
    }

    public function setScenario(?Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin = null): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

}
