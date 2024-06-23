<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="categories")
     */
    private Collection $posts;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $created_at;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->updated_at = new DateTime();
        $this->created_at = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUserEnt(): array
    {
        $user = $this->getUser();
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }


    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'user' => $this->getUserEnt(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
