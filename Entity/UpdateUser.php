<?php

namespace SchulIT\IdpExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="update_user")
 * @ORM\HasLifecycleCallbacks()
 */
class UpdateUser {

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime = null;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UpdateUser
     */
    public function setUsername(string $username): UpdateUser {
        $this->username = $username;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime(): \DateTime {
        return $this->dateTime;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setDateTime() {
        $this->dateTime = new \DateTime();
    }
}