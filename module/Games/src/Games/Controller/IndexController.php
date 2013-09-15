<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Games\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;
use Games\Entity\Available as Available;
use Games\Entity\Games as Game;
use Games\Entity\Players as Player;
use Games\Helpers\ttcTurnStrategy as ttcTurnStrategy;

class IndexController extends AbstractActionController
{
    protected $em;


    public function getEntityManager(){
        if(null === $this->em){
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }


    /**
     * SHOW AVAILABLE GAMES (TIC TAC TOE, MONOPOLY, ETC
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $games_available = $em->getRepository('Games\Entity\Available')->findAll();

        // Retrieve all of the available games from the db

       $layout = $this->layout();
       $layout->setTemplate('layout/dashboard');




        return array(
          'games_available' => $games_available
        );

    }

    /**
     * THIS IS THE PAGE THAT SHOWS THE GAMES THAT ARE AWAITING PLAYERS AND HAS A LINK TO CREATE A NEW GAME
     */
    public function pendingAction(){
        $game_type_id = $this->params()->fromRoute('id');

        // QUERY THE GAMES TABLE WITH THE GAME TYPE ID
        $em = $this->getEntityManager();
        $games_pending = $em->getRepository('Games\Entity\Games')->findBy(array('game_type' => $game_type_id, 'status' => 'pending'));

        /**
         * DATA NEEDED BY THE VIEW INCLUDES
         *  - THE USERNAME OF THE PLAYER ATTACHED TO THE GAME
         *  - THE TIME THE GAME WAS STARTED
         *
         */

        return array(
            'games_pending' => $games_pending,
            'game_type_id' => $game_type_id
        );



    }

    /**
     * THIS IS THE ACTION THAT IS CALLED WHEN A USER CLICKS TO START A NEW GAME
     *  - This action is called either behind the scenes or it redirects back to the pending action
     */
    public function startAction(){
       // NEED TO PASS THE GAME TYPE IN THE ROUTE
       $game_type_id = $this->params()->fromRoute('id');



        // GATHER THE USER ID FROM THE IDENTITY
        if($user = $this->identity()){
           // GET AN AVAILABLE GAME ENTITY WITH THE GAME TYPE ID
           $em = $this->getEntityManager();


           $game_type = $em->find('Games\Entity\Available', $game_type_id);

           $datetime = new \DateTime("now");

            // CREATE THE GAME
           $game = new Game();
           $game->setGameType($game_type)->setTimeStarted($datetime)->setStatus('pending')->setMode(1);
           $em->persist($game);


           // CREATE A NEW PLAYER RECORD WITH THE USER
           $player = new Player();
            /**
             * FOR NOW THE PLAYER THAT STARTS THE GAME WILL ALWAYS BE X
             */
            $turn_strategy = new ttcTurnStrategy();
            $turn_strategy->setStatus(1);
            $player->setTimeJoined($datetime)->setTurn($turn_strategy)->setOutcome('1')->setMark('x');
           $em->persist($player);

            $user->getPlayers()->add($player);
            $player->setUser($user);

            $game->getPlayers()->add($player);
            $player->setGame($game);
            $em->flush();
       }


        return $this->redirect()->toRoute('games', array('action' => 'pending', 'id' => $game_type_id));

    }



    public function joinAction(){
        // WHAT INFORMATION DO WE NEED
        // GAME ID
        $em = $this->getEntityManager();

        $game_id = $this->params()->fromRoute('id');

        // RETRIEVE THE GAME
        $game = $em->find('Games\Entity\Games', intval($game_id));
        $game_type = $game->getGameType();
        $game_name = $game_type->getName();
        $game_name_nospace = str_replace(" ", "", $game_name);
        $game_name_dashes = str_replace(" ", "-", $game_name);
        // make game name dashes lower case
        $game_name_dashes = strtolower($game_name_dashes);

        $datetime = new \DateTime("now");

        if($user = $this->identity()){

            /**
             * THE TURN WAS SET TO TRUE FOR THE PLAYER WHO CREATED THE GAME
             * CAN SAFELY SET THIS PLAYERS TURN TO 0 (FALSE) FOR NOW)
             */
            $player = new Player();
            $player->setTimeJoined($datetime)->setOutcome('1')->setMark('o');
            $em->persist($player);
            /**
             * FOR NOW THE PLAYER THAT 'JOINS' THE GAME WILL ALWAYS BE O
             */



            // ASSIGN THE PLAYER TO THE USER
            $user->getPlayers()->add($player);
            $player->setUser($user);

            // ASSIGN THE PLAYER TO THE GAME
            $game->getPlayers()->add($player);
            $player->setGame($game);

            /**
             * IF THIS WAS A DIFFERENT GAME LIKE MONOPOLY, WE WOULD NOT BE ABLE TO SAY IF THIS AS THIS PLAYERS TURN OR NOT
             * BECAUSE ALL OF THE PLAYERS WOULD FIRST HAVE TO JOIN
             * THEN THEY WOULD ROLL DICE TO DETERMINE WHO WOULD GO
             * SO, IT IS A BAD IDEA TO HAVE A STATUS PARAMETER IN THE TURN STRATEGY ABSTRACT CLASS...
             */
            $turn_strategy = new ttcTurnStrategy($player, $em);
            $turn_strategy->setStatus(0);
            $player->setTurn($turn_strategy);

            $em->flush();



            // FIND OUT HOW MANY PLAYERS THIS GAME TYPE REQUIRES
            // FIND THE GAME TYPE FROM THE GAME OBJECT
            $game_type = $game->getGameType();
            $minimum_players_needed = $game_type->getMinimumPlayers();

            // HOW MANY PLAYERS DOES THIS GAME NOW HAVE
            /**
             * THIS MAY NOT BE THE CORRECT WAY TO GET THE COUNT
             */
            $player_count = $game->getPlayers()->count();

            // NOW UPDATE THE GAME STATUS TO ACTIVE
            if($player_count >= $minimum_players_needed){
                $game->setStatus('active');
                $em->persist($game);
                $em->flush();
                // IN THIS CASE REDIRECT TO THE PLAY ACTION
                $player_id = $player->getId();
                return $this->redirect()->toRoute($game_name_nospace,  array('action' => "index", 'game_id' => $game_id, 'player_id' => $player_id));
               // $url = "/".$game_name_dashes."/".$game_id;
                //return $this->redirect()->toUrl($url);
            } else {
                // IN THIS CASE THE PLAYER COUNT THAT IS DISPLAYED IN THE GAME BOX NEEDS TO BE UPDATED
                // THIS WILL BE DONE WITH AJAX
            }

        }


    }



    public function playAction(){
        // CREATE A PLAYER OBJECT
        // IT SHOULD HAVE A USER AND GAME OBJECT WITHIN IT
        $em = $this->getEntityManager();
        $game = $em->getRepository('Games\Entity\Games')->find(1);

            $players = $game->getPlayers();
           $time =  $players[0]->getTimeJoined();
            echo $time;


        echo "test";

    }




}
