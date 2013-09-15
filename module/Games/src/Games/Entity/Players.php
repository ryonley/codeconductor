<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Games\Helpers\BaseClasses\TurnStrategy;

/**
 * @ORM\Entity
 */
class Players
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Games", inversedBy="players")
     */
    protected $game;

    protected $game_id;



    /**
     * @ORM\ManyToOne(targetEntity="RelyAuth\Entity\User", inversedBy="players")
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $turn;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time_joined;

    /**
     * @ORM\Column(type="string")
     */
    protected $outcome;

    /**
     * @ORM\Column(type="string")
     */
    protected $mark;

    /**
     * @param mixed $mark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;
    }

    /**
     * @return mixed
     */
    public function getMark()
    {
        return $this->mark;
    }




    public function hasPendingGame(){
        $gameStatus = $this->game->getStatus();
        if('pending' == $gameStatus) return true;
            else return false;
    }



    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;

    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
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
     * @param mixed $outcome
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * @param mixed $time_joined
     */
    public function setTimeJoined($time_joined)
    {
        $this->time_joined = $time_joined;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeJoined()
    {
        return $this->time_joined;
    }

    /**
     * @param mixed $turn
     */
    public function setTurnold($turn)
    {
        $this->turn = $turn;
        return $this;
    }

    /**
     * SET TURN METHOD NEEDS TO FIGURE OUT WHO'S TURN IT IS
     * IT NEEDS TO COMPARE THE TIMESTAMPS OF ALL PLAYERS FOR THIS GAME AND
     */
    public function setTurn($turn_strategy)
    {
        // You can either pass a turn strategy to this setter
        // or a boolean

        if(is_object($turn_strategy)){
            if(is_subclass_of($turn_strategy, 'Games\Helpers\BaseClasses\TurnStrategy')){
                $this->turn = $turn_strategy->getStatus();
            } else {
                throw new Exception('The set turn method only accepts objects that inherit from TurnStrategy');
            }
        } elseif(is_bool($turn_strategy)) {
            if($turn_strategy == true) {
                $this->turn = 1;
            } else {
                $this->turn = 0;
            }
        } else {
            throw new Exception('The players set turn method must be passed either an object or a boolean');
        }


        return $this;
    }


    /**
     * @return mixed
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }





}