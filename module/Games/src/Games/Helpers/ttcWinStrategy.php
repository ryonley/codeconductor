<?php
namespace Games\Helpers;

use Games\Helpers\BaseClasses\WinStrategy as WinStrategy;

class ttcWinStrategy extends WinStrategy
{
    protected $winning_positions;

    protected $winning_combinations = array(
        array('1', '2', '3'),
        array('4', '5', '6'),
        array('7', '8', '9'),

        array('1', '4', '7'),
        array('2', '5', '8'),
        array('3', '6', '9'),

        array('1', '5', '9'),
        array('3', '5', '7')
    );

    public function setWinner(){

        // GET THE MOVES AND THEN POSITIONS OF ONE PLAYER AT A TIME
        //FOREACH PLAYER SEND ALL OF THEIR POSITIONS IDS TO A threeInARow CHECKER
        // Need an array of arrays with the player id as the key and the sub array would include all of the positions
        // they currently occupy
        $players_moves = $this->getPlayersArray();

        foreach($players_moves as $player_id => $position_ids){

            // THE FOLLOWING WILL RETURN AN ARRAY OF THE WINNING POSITION IDS OR FALSE IF THERE ARE NOT 3 IN A ROW
            $result = $this->threeInARow($position_ids);
            if(is_array($result) && 3 == count($result)){
                $this->winner = $player_id;
                $this->winning_positions = $result;

                return;
            }
        }
        $this->winner = false;
        $this->winning_positions = false;

    }

    public function getWinner(){
        $this->setWinner();
        return $this->winner;
    }

    public function getWinningPositions(){
        return $this->winning_positions;
    }

    protected function threeInARow($position_ids){
        foreach($this->winning_combinations as $wc){
            // COULD DO THE FOLLOWING
            $result = array_intersect($wc, $position_ids);
            if(3 == count($result)) return $result;
        }
        return false;
    }

    protected function getPlayersArray(){
        $players_moves = array();
        foreach($this->moves as $move){
                $player_id = $move->getPlayer()->getId();
                $position_id = $move->getPosition()->getId();
                $players_moves[$player_id][] = $position_id;
        }
        return $players_moves;
    }
}