<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rodger
 * Date: 9/15/13
 * Time: 10:06 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Games\Helpers\BaseClasses;


abstract class GameOver {

    protected $em;
    protected $moves;

    public function __construct($em){
        $this->em = $em;
    }

    public function setMoves($moves){
        $this->moves = $moves;
        return $this;
    }

    abstract public function isOver();

}