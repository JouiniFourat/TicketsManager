<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
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
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Export", inversedBy="allTickets")
     */
    private $export;


    public function getId(): ?int
    {
        return $this->id;
    }

public
function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
{
    $this->content = $content;

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

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): self
{
    $this->lastUpdate = $lastUpdate;

    return $this;
}

    public function getStat(): ?string
    {
        return $this->stat;
    }

    public function setStat(string $stat): self
{
    $this->stat = $stat;

    return $this;
}

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getExport(): ?Export
    {
        return $this->export;
    }

    public function setExport(?Export $export): self
    {
        $this->export = $export;

        return $this;
    }

    public function description()
    {
       return $this->id.' '.$this->content.' '.$this->creationDate->format("Y-m-d H:i:s").' '.$this->lastUpdate->format("Y-m-d H:i:s").' '.$this->user.' '.$this->project.' '.$this->stat;
    }


}
