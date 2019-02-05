<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvatarRepository")
 */
class Avatar implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    private $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="avatar")
     */
    private $user;

    //Because of this error : "Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed" => have to implement \Serializable
    public function serialize()
    {
        return serialize(array(
          $this->id,
          $this->name,
          $this->user
        ));
    }

    public function unserialize($serialized)
    {
        list (
          $this->id,
          $this->name,
          $this->user
            ) = unserialize($serialized);
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
