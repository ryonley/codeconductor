<?php
namespace Games\Helpers\BaseClasses;



abstract class TurnStrategy{

    protected $player;
    protected $em;

    /**
     * @param $status
     * @param string $player
     * @param string $em
     *
     * THE STATUS WILL NOT ALWAYS BEEN KNOWN INITIALLY
     * AND THE SET STATUS METHOD SHOULD PROBABLY NOT BE CALLED EVERY TIME IN THE CONSTRUCT
     */
    public function __construct($player='', $em = ''){

        $this->player = $player;
        $this->em = $em;

    }

    public function getStatus(){
        return $this->status;
    }

    abstract public function setStatus($status);

    abstract public function setOtherPlayersStatus();
}