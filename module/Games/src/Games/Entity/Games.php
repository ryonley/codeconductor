<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Games
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Available", inversedBy="games")
     */
    protected $game_type;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time_started;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="integer")
     */
    protected $mode;

    /**
     *  @ORM\OneToMany(targetEntity="Players", mappedBy="game")
     */
    protected $players;

    /**
     *  @ORM\OneToMany(targetEntity="Moves", mappedBy="game")
     */
    protected $moves;

    public function __construct(){
        $this->players = new ArrayCollection();
        $this->moves = new ArrayCollection();
    }

    public function getPlayers(){
        return $this->players;
    }

    public function getMoves(){
        return $this->moves;
    }

    public function getNewerMove($timestamp){
        $passed_time = strtotime($timestamp);
        // LOOP THROUGH THE MOVES AND IF THERE IS A NEWER MOVE, RETURN IT
        if(!empty($this->moves)){
            foreach($this->moves as $move){
                $this_stamp = $move->getTimestamp();
                $this_stamp = $this_stamp->format('Y-m-d H:i:s');
                $this_time = strtotime($this_stamp);
                if($this_time > $passed_time){
                    return $move;
                    break;
                }

            }
            return false;
        }
        return false;
    }


    /**
     * @param mixed $game_type
     */
    public function setGameType($game_type)
    {
        $this->game_type = $game_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGameType()
    {
        return $this->game_type;
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
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $time_started
     */
    public function setTimeStarted($time_started)
    {
        $this->time_started = $time_started;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeStarted()
    {
        return $this->time_started;
    }



}