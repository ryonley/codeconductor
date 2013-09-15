<?php
namespace Games\Helpers\BaseClasses;

abstract class WinStrategy{
    protected $moves;
    protected $winner;

    public function _construct(){

    }

    public function setMoves($moves){
        $this->moves = $moves;
        return $this;
    }

    abstract function setWinner();

    abstract function getWinner();
}