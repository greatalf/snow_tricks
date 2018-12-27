<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    /**
     * 
     */
    private $oldPass;
    
    /**
     * @Assert\Length(min=6)
     */
    private $newPass;
    
    /**
     * @Assert\EqualTo(propertyPath="newPass", message="Les mots de passe saisis ne sont pas identiques")
     */
    private $confirmPass;

    public function getOldPass(): ?string
    {
        return $this->oldPass;
    }

    public function setOldPass(string $oldPass): self
    {
        $this->oldPass = $oldPass;

        return $this;
    }

    public function getNewPass(): ?string
    {
        return $this->newPass;
    }

    public function setNewPass(string $newPass): self
    {
        $this->newPass = $newPass;

        return $this;
    }

    public function getConfirmPass(): ?string
    {
        return $this->confirmPass;
    }

    public function setConfirmPass(string $confirmPass): self
    {
        $this->confirmPass = $confirmPass;

        return $this;
    }
}
