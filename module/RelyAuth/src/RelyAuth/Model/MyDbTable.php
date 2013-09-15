<?php

namespace RelyAuth\Model;

use Zend\Authentication\Adapter\DbTable;
use Zend\Db\Sql\Expression as SqlExpr;
use Zend\Db\Sql\Predicate\Operator as SqlOp;

class MyDbTable extends DbTable{
    
    protected $cryptStrategy;
    
    
    // TO DO... SET A TEST TO MAKE SURE THE PARAMETER IS AN ELIGIBLE CRYPT STRATEGY
    public function setCryptStrategy($strategy){
        $this->cryptStrategy = $strategy;
    }
    
        /**
     * _authenticateCreateSelect() - This method creates a Zend\Db\Sql\Select object that
     * is completely configured to be queried against the database.
     *
     * @return DbSelect
     */
    protected function _authenticateCreateSelect()
    {
        // build credential expression
        if (empty($this->credentialTreatment) || (strpos($this->credentialTreatment, '?') === false)) {
            $this->credentialTreatment = '?';
        }
        
        if(isset($this->credential)){
            // ENCRYPT THE CREDENTIAL USING THE CRYPT STRATEGY
            $this->credential = $this->cryptStrategy->create($this->credential);
        }

        $credentialExpression = new SqlExpr(
            '(CASE WHEN ?' . ' = ' . $this->credentialTreatment . ' THEN 1 ELSE 0 END) AS ?',
            array($this->credentialColumn, $this->credential, 'zend_auth_credential_match'),
            array(SqlExpr::TYPE_IDENTIFIER, SqlExpr::TYPE_VALUE, SqlExpr::TYPE_IDENTIFIER)
        );

        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->tableName)
            ->columns(array('*', $credentialExpression))
            ->where(new SqlOp($this->identityColumn, '=', $this->identity));

        return $dbSelect;
    }
    
    
}
?>
