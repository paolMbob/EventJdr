<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonnageJoueurRepository")
 */
class PersonnageJoueur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeJoueur", inversedBy="Pj")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pointExperience;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Session", mappedBy="personnageJoueur")
     */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="pj")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRace(): ?TypeJoueur
    {
        return $this->race;
    }

    public function setRace(?TypeJoueur $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getPointExperience(): ?int
    {
        return $this->pointExperience;
    }

    public function setPointExperience(?int $pointExperience): self
    {
        $this->pointExperience = $pointExperience;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->addPersonnageJoueur($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removePersonnageJoueur($this);
        }

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

}
