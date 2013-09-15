<?php
namespace RelyAuth\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select,
    Zend\Db\ResultSet\ResultSet;

class UsersTable
{
    protected $tableGateway;
    protected $sm;

    public function __construct(TableGateway $tableGateway, $sm)
    {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
    }
  
        /*
    public function getUser($username){
        $rowset = $this->tableGateway->select(array('username' => $username));
        $row = $rowset->current();
        if(!$row){
            throw new \Exception("Could not find user $username");
        }
        return $row;
    }
    

    public function fetchUser($username){
        $select = new \Zend\Db\Sql\Select;
        $select->from('users');
        $select->columns(array('username', 'real_name'));
        $select->join('user_role_linker', "users.id = user_role_linker.user_id", array('role_id'), 'left');
        $select->where(array('username' => $username));
        echo $select->getSqlString();
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
     * 
     */
    
    // JOINS TWO TABLES
    public function fetchUser($username){
         $select = new Select;
         $select->from('users')->join('user_role_linker', "users.id = user_role_linker.user_id", array('role_id'))->where(array('username' => $username));
         $adapter = $this->sm->get('dbAdapter');
         $statement = $adapter->createStatement();
         $select->prepareStatement($adapter, $statement);
         $resultSet = new ResultSet();
         $resultSet->initialize($statement->execute());
         return $resultSet->current();
    }
    
}
?>
