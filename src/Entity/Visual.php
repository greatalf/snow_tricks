<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\VisualRepository")
 */
class Visual
{
    const VISUALKIND = [
        0 => 'Image',
        1 => 'Vidéo'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=50)
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure", inversedBy="visuals", cascade="persist")
     * @ORM\JoinColumn(nullable=false)
     */
    private $figure;

    /**
     * @ORM\Column(type="integer")
     */
    private $visualKind = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

        return $this;
    }

    public function getVisualKind(): ?int
    {
        return $this->visualKind;
    }

    public function setVisualKind(int $visualKind): self
    {
        $this->visualKind = $visualKind;

        return $this;
    }

    public function getVisualKindType(): string
    {
        return self::HEAT[$this->visualKind];
    }

    public function isImage()
    {
        $extTable = ['.jpg', '.jpeg', '.png', 'aspx'];
        $extensionJpgPng = (substr($this->getUrl(), strlen($this->getUrl())-4));
        $extensionJpeg = (substr($this->getUrl(), strlen($this->getUrl())-5));
        
        if(in_array($extensionJpgPng, $extTable) || in_array($extensionJpeg, $extTable))
        {
            $this->setVisualKind('0');
            return true;
        }
        return false;
    }
}
