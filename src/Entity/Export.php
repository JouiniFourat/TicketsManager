<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExportRepository")
 */
class Export
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
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="export")
     * @ORM\OrderBy({"project" = "ASC"});
     */
    private $allTickets;

    public function __construct()
    {
        $this->allTickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getAllTickets(): Collection
    {
        return $this->allTickets;
    }

    public function addAllTicket(Ticket $allTicket): self
    {
        if (!$this->allTickets->contains($allTicket)) {
            $this->allTickets[] = $allTicket;
            $allTicket->setExport($this);
        }

        return $this;
    }

    public function removeAllTicket(Ticket $allTicket): self
    {
        if ($this->allTickets->contains($allTicket)) {
            $this->allTickets->removeElement($allTicket);
            // set the owning side to null (unless already changed)
            if ($allTicket->getExport() === $this) {
                $allTicket->setExport(null);
            }
        }

        return $this;
    }
}
