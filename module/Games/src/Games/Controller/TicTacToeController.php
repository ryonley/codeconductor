<?php

namespace Games\Controller;

use Games\Entity\Moves;
use Games\Helpers\ttcWinStrategy;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;
use Doctrine\Common\Collections\Criteria;
use Zend\Session\Container as SessionContainer;
use Games\Helpers\ttcTurnStrategy as ttcTurnStrategy;
use Games\Helpers\ttcGameOver as ttcGameOver;

class TicTacToeController extends AbstractActionController
{
    protected $em;
    protected $sesscontainer;

    public function getEntityManager(){
        if(null === $this->em){
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    private function getSessContainer()
    {
        if (!$this->sesscontainer) {
            $this->sesscontainer = new SessionContainer();
        }
        return $this->sesscontainer;
    }





    // THE PLAY ACTION WILL  FIND OUT WHO'S TURN AND NOTIFY THEM

    // NEED A WAY TO MAP A POSITION FROM THE POSITIONS TABLE TO A PLACE ON THE SCREEN (IN THE VIEW)

    // IN THE PLAY ACTION, AFTER THE USER IS NOTIFIED OF THEIR TURN, THAT USER HAS THE ABILITY TO MAKE A "MOVE" ..
    // IN THIS CASE, THERE IS NO LISTENER RUNNING.... THE LISTENER IS IMPLEMENTED BASED ON WHO'S TURN IT IS..
    // IF IT IS NOT YOUR TURN, THEN YOU CANNOT MAKE A MOVE AND A LISTENER IS ENGAGED

    // IF IT IS YOUR TURN YOU CAN MAKE A MOVE... YOU SUBMIT YOUR MOVE VIA AJAX, THE DATABASE IS UPDATED, YOUR TURN IS SET TO 0, AND THE LISTENER IS ENGAGED FOR YOU
    // ONCE IT DETERMINES THAT A MOVE HAS BEEN MADE, IT UPDATES THE VIEW WITH THE MOVES
    // AND LETS YOU KNOW IF IT IS YOUR TURN OR NOT
    // AND DECIDES WHEATHER TO KEEP THE LISTENER ENGAGED
    public function indexAction(){
        $em = $this->getEntityManager();

        $game_id = $this->params()->fromRoute('game_id');
        $player_id = $this->params()->fromRoute('player_id');
        $sc = new SessionContainer();
        $sc->game_id = $game_id;
        $sc->player_id = $player_id;

        $game = $em->find('Games\Entity\Games', $game_id);
        $moves = $game->getMoves();

        // EACH MOVE HAS ONE POSITION
        // MOVES SHOULD BE A COLLECTION OF MOVES
        $taken = array();

        if(!$moves->isEmpty()){
            $recordtime = 0;
            foreach($moves as $move){
                $position = $move->getPosition();
                $datetime = $move->getTimestamp();
                $thisdatetime = $datetime->format('Y-m-d H:i:s');
                $thistime = strtotime($thisdatetime);
                // WE ARE FINDING THE MOST RECENT TIME
                if($thistime > $recordtime) $recordtime = $thistime;

                $mark = $move->getPlayer()->getMark();
                $taken[$position->getId()] = $mark;
            }
            // SET THE MOST RECENT TIME AS THE MOVE_TIME
            $move_time = $recordtime;
        } else {
            $move_time = date('Y-m-d H:i:s');
        }


        $user = $this->identity();

        // FIND OUT IF IT IS THIS PLAYERS TURN
        $player = $em->find('Games\Entity\Players', $player_id);
        $turn = $player->getTurn();






        // IF TURN IS TRUE, THE USER WILL BE ABLE TO CLICK THE TICK TAC TOE ELEMENTS IN THE VIEW
        // OTHERWISE THEY WILL BE DISABLED

        // IN TIC TAC TOE THE TURN IS SET TO TRUE FOR THE PLAYER WHO STARTS THE GAME

        return array(
            'turn' => $turn,
            'taken' => $taken,
            'move_time' => $move_time,
            'game_id' => $game_id

        );


    }

    /**
     * THIS ACTION FIRST MAKES SURE THE POSITION HAS NOT ALREADY BEEN TAKEN
     * IT CREATES A RECORD IN THE MOVES TABLE FOR THIS POSITION
     * SETS THE PLAYERS TURN TO 0
     * LETS THE CLIENT KNOW IF OPERATIONS WERE SUCCESSFULL OR NOT..
     * ALSO IT TELLS THE CLIENT WHETHER TO MARK WITH AN X OR AN O
     */
    public function moveAction(){
        // INFO NEEDED
        // POSITION ID
        // PLAYER ID
        // GAME ID

        $game_id = $this->getSessContainer()->game_id;
        $player_id = $this->getSessContainer()->player_id;

        $request = $this->getRequest();
        $response = $this->getResponse();

        if($request->isPost()){
            $post_data = $request->getPost();
            $position_id = $post_data['position'];

            $em = $this->getEntityManager();
            // GET THE  GAME OBJECT, PLAYER OBJECT, AND POSITION OBJECT
            $game = $em->find('Games\Entity\Games', $game_id);
            $player = $em->find('Games\Entity\Players', $player_id);
            $position = $em->find('Games\Entity\Positions', $position_id);

            // MAKE SURE THERE IS NOT ALREADY A RECORD IN THE MOVES TABLE FOR THIS POSITION AND GAME

            $moves = $em->getRepository('Games\Entity\Moves')->findBy(array('game' => $game_id, 'position' => $position_id));

            if(!empty($moves)){
                // THIS POSITION WAS ALREADY TAKEN
                $success = false;
            } else {
                // THIS POSITION IS AVAILABLE... CREATE THE RECORD
                $datetime = new \DateTime("now");
                $move_time = $datetime->format('Y-m-d H:i:s');

                $move = new Moves();
                $em->persist($move);
                $move->setGame($game)->setPlayer($player)->setPosition($position)->setTimestamp($datetime);
                $em->flush();
                /**
                 * HERE IS WHERE WE CHECK EVERYTIME TO SEE IF THERE IS A WINNER YET
                 * CREATE A METHOD IN THE GAMES ENTITY CALLED checkForWinner
                 *
                 * CREATE A WIN STRATEGY OBJECT THAT THE GAMES ENTITY WILL USE IN THE CHECKFORWINNER METHOD
                 *
                 */
                $success = true;
                $game_is_over = false;
                $winner_id = false;
                $winning_positions = false;
                $custom_text = '';

                $win_strategy = new ttcWinStrategy();
                $game_over = new ttcGameOver($em);
                $winner = $game->checkForWinner($win_strategy);
                if($winner){
                    $game_is_over = true;
                    // IF THERE IS A WINNER THEN THERE IS NO NEED TO SET THE TURN
                    // SEND THE WINNER'S PLAYER ID
                    $winner_id = $winner;
                    if($winner_id == $player_id) $custom_text = "You win!";
                        else $custom_text = "You lose";
                    $winning_positions = $win_strategy->getWinningPositions();
                } elseif($game->checkGameOver($game_over)){
                    $game_is_over = true;
                    $custom_text = "Game Over";
                } else {
                    $turn_strategy = new ttcTurnStrategy($player, $em);
                    $turn_strategy->setStatus(0);
                    // SET TURN WILL SET THIS SPECIFIC PLAYERS TURN TO FALSE AND SET THE TURN OF WHOEVER GOES NEXT
                    // WHAT DOES THE PLAYER OBJECT NEED FROM THE STRATEGY OBJECT TO:
                    // - SET THE CURRENT
                    $player->setTurn($turn_strategy);
                    $em->persist($player);
                    // SET THE TURN TO TRUE FOR WHOEVER'S TURN IT IS... SET TURN METHOD NEEDS TO DO THIS
                    $em->flush();

                }




            }

            //   NEED TO ALSO RETURN THE POSITION CLICKED AND
            // NEED TO SET THE TURN
             $response->setContent(\Zend\Json\Json::encode(array('success' => $success, 'mark' => $player->getMark(), 'timestamp' => $move_time, 'game_id' => $game_id, 'winner_id' => $winner_id,
                 'winning_positions' => $winning_positions, 'game_over' => $game_is_over, 'custom_text' => $custom_text, 'winner' => $winner)));
            return $response;
        }

    }


    /**
     * THE UPDATE FUNCTION NEEDS A WAY TO CHECK IF A CHANGE HAS BEEN MADE
     *
     * WE WILL SEND THE FUNCTION A TIMESTAMP AND IT WILL CHECK THE MOVES TABLE TO SEE IF THERE IS A MORE RECENT TIMESTAMP
     * FOR THIS GAME
     *
     * IF THERE IS, THE UPDATE ACTION WILL RETURN THE POSITION ID AND THE MARK OF THE PLAYER HOW MADE THE MOVE
     */
    public function updateAction(){
      // RECEIVE THE TIMESTAMP AND GAME_ID

      // CHECK THE MOVES TABLE FOR A RECORD WITH A TIMESTAMP GREATER THAN THAT PROVIDED AND A MATCHING GAME ID
      // IF IT EXISTS RETURN TRUE

      // NEEDS TO RETURN WHEThER OR NOT IT IS THE PLAYERS TURN

      // ALSO RETURN THE POSITION ID AND THE MARK OF THE PLAYER WHO MADE THE MOVE
        $player_id = $this->getSessContainer()->player_id;

        $success = false;
        $position_id = '';
        $mark = '';

        $request = $this->getRequest();
        $response = $this->getResponse();
        if($request->isPost()){
            $post_data = $request->getPost();
            $timestamp = $post_data['timestamp'];
            $game_id = $post_data['game_id'];

            $em = $this->getEntityManager();
            $game = $em->find('Games\Entity\Games', $game_id);
            $move = $game->getNewerMove($timestamp);

            $winner_id = false;
            $winning_positions = false;
            $game_is_over = false;
            $custom_text = '';

            if($move !== false){
                // GET THE POSITION OF THE MOVE
                // GET THE MARK OF THE PLAYER WHO MADE THE MOVE
                $success = true;
                $game_over = false;
                $position_id = $move->getPosition()->getId();
                $mark = $move->getPlayer()->getMark();

                // ALSO DETERMINE IF THIS WAS A WINNING MOVE
                $win_strategy = new ttcWinStrategy();
                $game_over = new ttcGameOver($em);

                if($winner = $game->checkForWinner($win_strategy)){
                    // IF THERE IS A WINNER THEN THERE IS NO NEED TO SET THE TURN
                    //$winner_id = $winner->getId();
                    $game_is_over = true;
                    $winner_id = $winner;
                    if($winner_id == $player_id) $custom_text = "You win!";
                    else $custom_text = "You lose";
                    $winning_positions = $win_strategy->getWinningPositions();
                } elseif($game->checkGameOver($game_over)){
                    $game_is_over = true;
                }
            }



            $response->setContent(\Zend\Json\Json::encode(array('position_id' => $position_id, 'mark' => $mark, 'success' => $success, 'winner_id' => $winner_id,
                'winning_positions' => $winning_positions, 'game_over' => $game_is_over, 'custom_text' => $custom_text)));
            return $response;
        }

    }

}