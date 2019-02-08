<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="user", orphanRemoval=true)
     */
    private $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->ticketsu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->password,
                $this->email
            ]
        );
    }

    public function unserialize($string)
    {
        list (
                $this->id,
                $this->username,
                $this->password,
                $this->email
             ) = unserialize($string, ['allowed_classes' => false]);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function __toString()
        {
        return $this->username;
        }

    /**
     * @return Collection|Ticket[]
     */
    public function getTicketsu(): Collection
    {
        return $this->ticketsu;
    }

    public function addTicketsu(Ticket $ticketsu): self
    {
        if (!$this->ticketsu->contains($ticketsu)) {
            $this->ticketsu[] = $ticketsu;
            $ticketsu->setUser($this);
        }

        return $this;
    }

    public function removeTicketsu(Ticket $ticketsu): self
    {
        if ($this->ticketsu->contains($ticketsu)) {
            $this->ticketsu->removeElement($ticketsu);
            // set the owning side to null (unless already changed)
            if ($ticketsu->getUser() === $this) {
                $ticketsu->setUser(null);
            }
        }

        return $this;
    }


}
