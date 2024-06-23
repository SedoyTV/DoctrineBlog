<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;


/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public int $id;

    /**
     * @ORM\Column(type="string")
     */
    public string $description;

    /**
     * @ORM\Column(type="string")
     */
    public string $title;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinTable(name="category_post")
     */
    public Collection $categories;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    /**
     * @ORM\Column(type="datetime")
     */
    public DateTime $updated_at;
    /**
     * @ORM\Column(type="datetime")
     */
    public DateTime $created_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function __construct() {
        $this->categories = new ArrayCollection();

    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }


    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getCategoryTitles(): array
    {
        $categories = [];
        foreach ($this->categories as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'user' => $category->getUserEnt()
            ];
        }
        return $categories;
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
            'description' => $this->getDescription(),
            'categories' => $this->getCategoryTitles(),
            'image' => $this->getImage(),
            'user' => $this->getUserEnt(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

