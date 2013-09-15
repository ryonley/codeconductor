<?php

namespace RelyAuth\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\table(name="user")
 * @property string $username
 * @property string $password
 * @property int $id
 */
class User implements InputFilterAwareInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $real_name;

    protected $inputFilter;


    public function __get($property){
        return $this->$property;
    }

    public function __set($property, $value){
        $this->property = $value;
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

    public function populate($data = array()){
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->real_name = (isset($data['real_name'])) ? $data['real_name'] : null;
    }
    /*
        public function exchangeArray($data){
              $this->id = (isset($data['id'])) ? $data['id'] : null;
              $this->username = (isset($data['username'])) ? $data['username'] : null;
               $this->password = (isset($data['password'])) ? $data['password'] : null;
                $this->real_name = (isset($data['real_name'])) ? $data['real_name'] : null;
        }
    */
    
    public function setInputFilter(InputFilterInterface $inputFilter) 
    {
         throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
         if(!$this->inputFilter){
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                   'filters' => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim')
                 ),
                 'validators' => array(
                     array(
                         'name' => 'EmailAddress'
                     )
                 )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' =>'password',
                'required' => true,
                 'filters' => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim')
                 ),
            )));
            
            $this->inputFilter = $inputFilter;
         }
         return $this->inputFilter;
    }
}
