<?php
namespace Games\Helpers;

use Games\Helpers\BaseClasses\WinStrategy as WinStrategy;

class ttcWinStrategy extends WinStrategy
{
    protected $winning_positions;

    protected $winning_combinations = array(
        array('1', '2', '3'),
        array('1', '5', '9'),
        array('3', '6', '9')
    );

    public function setWinner(){
        $moves = $this->moves;
        // GET THE MOVES AND THEN POSITIONS OF ONE PLAYER AT A TIME
        //FOREACH PLAYER SEND ALL OF THEIR POSITIONS IDS TO A threeInARow CHECKER
        foreach($players as $player){
            $position_ids = array();
            // THE FOLLOWING WILL RETURN AN ARRAY OF THE WINNING POSITION IDS OR FALSE IF THERE ARE NOT 3 IN A ROW
            $result = $this->threeInARow($position_ids);
            if(is_array($result) && 3 == count($result)){
                $this->winner = $player;
                $this->winning_positions = $result;
                break;
            }
        }
        $this->winner = false;
        $this->winning_positions = false;

    }

    public function getWinner(){
        $this->setWinner();
        return $this->winner;
    }

    protected function threeInARow($position_ids){
        foreach($this->winning_combinations as $wc){
            // COULD DO THE FOLLOWING
            $result = array_intersect($wc, $position_ids);
            if(3 == count($result)) return $result;
        }
        return false;
    }
}