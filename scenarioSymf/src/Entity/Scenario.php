<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ScenarioRepository")
 */
class Scenario
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
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Univers", inversedBy="scenarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $univers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="scenario", orphanRemoval=true)
     */
    private $commentaires;

    /**
     *@ORM\Column(type="string", length=255, nullable=true)
     *@Assert\NotBlank(message="veuillez télécharger un fichier au format jpeg.")
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="scenario")
     */
    private $sessions;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->sessions = new ArrayCollection();
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getUnivers(): ?Univers
    {
        return $this->univers;
    }

    public function setUnivers(?Univers $univers): self
    {
        $this->univers = $univers;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setScenario($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getScenario() === $this) {
                $commentaire->setScenario(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

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
            $session->setScenario($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getScenario() === $this) {
                $session->setScenario(null);
            }
        }

        return $this;
    }
}
