<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post
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
    public string $title;

    /**
     * @ORM\Column(type="text")
     */
    public string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTime $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTime $updated_at;

    public function __construct()
    {

        $this->updated_at = new \DateTime();
        $this->created_at = new \DateTime();
    }
}
