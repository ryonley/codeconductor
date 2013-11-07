<?php
namespace Games\Helpers;

use Games\Helpers\BaseClasses\GameOver as GameOver;

class ttcGameOver extends GameOver {

    public function isOver(){
        // DETERMINE (BASED ON THE MOVES) IF THIS GAME IS CONSIDERED OVER DUE TO NO OTHER POSSIBLE MOVES
        // 1ST QUERY THE DB FOR ALL POSITION IDS FOR THIS AVAILABLE GAME (1 FOR TIC TAC TOE)
        $positions = $this->em->getRepository('Games\Entity\Positions')->findBy(array('available_game' => '1'));
        // NEED AN ARRAY OF ALL POSITION IDS
        $position_ids = array();
        foreach($positions as $position){
            $position_ids[] = $position->getId();
        }

        // NEED AN ARRAY OF ALL POSITIONS IN THIS GAME
        $position_ids_this_game = array();
        foreach($this->moves as $move){
            $position_ids_this_game[] = $move->getPosition()->getId();
        }

        // SINCE THERE CAN NOT BE DUPLICATE POSITIONS IN A GAME
        // WE ONLY NEED TO CHECK THAT THE NUMBER OF POSITIONS IDS IN THIS GAME MATCHES THE TOTAL NUMBER OF POSITIONS POSSIBLE FOR THIS GAME
        // IF THE NUMBERS MATCH // NO MORE MOVES CAN BE MADE // GAME OVER
        if(count($position_ids_this_game) == count($position_ids)){
            return true;
        } else {
            return false;
        }

    }
}