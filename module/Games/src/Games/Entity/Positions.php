<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Positions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Available")
     */
    protected $available_game;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @param mixed $available_game
     */
    public function setAvailableGame($available_game)
    {
        $this->available_game = $available_game;
    }

    /**
     * @return mixed
     */
    public function getAvailableGame()
    {
        return $this->available_game;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

}