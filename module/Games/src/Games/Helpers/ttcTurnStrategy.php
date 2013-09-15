<?php
namespace Games\Helpers;

use Games\Helpers\BaseClasses\TurnStrategy as TurnStrategy;

class ttcTurnStrategy extends TurnStrategy
{
    /*
    public function setStatusOld(){
        // THIS METHOD WILL BE CALLED BY THE CONSTRUCTOR, ONLY IF IT IS NOT THIS PLAYERS TURN
        // SINCE THIS IS TIC TAC TOE, SIMPLY FIND THE OTHER PLAYER AND MARK THEIR STATUS AS TRUE
        $player_id = $this->player->getId();
        $game = $this->player->getGame();
        $game_id = $game->getId();


        // FIND THE PLAYER WITH THE SAME GAME ID AS ABOVE BUT A DIFFERENT PLAYER ID
        // MAY NEED TO USE QUERY BUILDER
        $qb = $this->em->createQueryBuilder();


        $qb->update('players', 'p')
            ->set('turn', 1)
            ->where('p.game_id = ?1 AND p.id != ?2')
            ->setParameters(array(1 => $game_id, 2 => $player_id));
        $query = $qb->getQuery();
        $result = $query->getResult();

    } */

    /**
     * THE TIC TAC TOE TURN STRATEGY WILL
     */
    public function setStatus($status){
        $this->status = $status;
        $this->setOtherPlayersStatus();
    }

    /**
     * IF THIS PLAYERS STATUS IS FALSE, IT MEANS THAT THE OTHER PLAYERS STATUS IS AUTOMATICALLY TRUE (MEANING ITS THERE TURN)
     * HOWEVER IN THIS CASE, THEIR STATUS (TURN)WAS ALREADY SET TO TRUE WHEN THEY CREATED THE GAME
     *
     * *HOWEVER FOR SUBSEQUENT CALL'S TO SET STATUS WE WILL NEED TO CHANGE THE STATUS OF THE OTHER PLAYER
     */
    public function setOtherPlayersStatus(){
       // NO NEED TO DO ANYTHING
        if($this->status == 0){
            // FIND THE OTHER PLAYER AND SET STATUS TO TRUE
            $player_id = $this->player->getId();
            $game = $this->player->getGame();
            $game_id = $game->getId();

            // WE ONLY NEED TO DO ANYTHING IF THE PLAYER ID IS NOT NULL
            // IF THE PLAYER ID IS NULL, THIS PLAYER HAS NOT BEEN FLUSHED TO THE DB YET
            // WHICH MEANS THEY JUST JOINED THE GAME AND THE OTHER PLAYER'S STATUS HAS ALREADY BEEN SET
            if(is_int($player_id)){
                // INSTEAD NEED TO RETREIVE THE OTHER PLAYER
                // UPDATE THE TURN STATUS
                // AND FLUSH
                $qb = $this->em->createQueryBuilder();
                $qb->select('p')
                    ->from('Games\Entity\Players', 'p')
                    ->where('p.id != ?1')
                    ->setParameter(1, $player_id);

                $result = $qb->getQuery()->getResult();

                if(is_array($result) && !empty($result)){
                    $other_player = $result[0];
                    $other_player->setTurn(true);
                    $this->em->persist($other_player);

                }

            }

        }
    }

}