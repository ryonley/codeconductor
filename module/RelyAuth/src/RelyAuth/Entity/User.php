<?php

namespace RelyAuth\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Crypt\Password\Bcrypt;

use Zend\InputFilter\Input;
use Zend\Validator;
use Games\Entity\Players;

/**
 * @ORM\Entity
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

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

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     *
     */
    protected $role;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    protected $registered;

    /**
     *  @ORM\Column(type="string")
     *
     */
    protected $status;


    /**
     * @ORM\Column(type="datetime")
     *
     */
    protected $last_login;

    /**
     * @ORM\Column(type="string")
     *
     */
    protected $token;






    /**
     *  @ORM\OneToMany(targetEntity="Games\Entity\Players", mappedBy="user")
     */
    protected $players;




    protected $inputFilter;
    protected $registrationFilter;



    const SALT = '1234567890123456';




    public function __construct($sm = ''){
        $this->players = new ArrayCollection();
        $this->sm = $sm;
    }


    public function hasPendingGame(){
       foreach($this->players as $player){
           if($player->hasPendingGame()) return true;
       }
       return false;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }






    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }





    public function getRealName(){
        return $this->real_name;
    }

    public function getRole(){
        return $this->role;
    }



    public function getUserName(){
        return $this->username;
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

    public function getPassword(){
        return $this->password;
    }

    public function getToken(){
        return $this->token;
    }

    public function setPassword($password){
        $bcrypt = new Bcrypt(array(
            'salt' => self::SALT,
            'cost' => 14
        ));
        $this->password = $bcrypt->create($password);
        return $this;
    }


    public static function hashPassword($user, $password){
        $c = get_called_class();
        $salt = $c::SALT;
        $bcrypt = new Bcrypt(array(
            'salt' => $salt,
            'cost' => 14
        ));
        if($bcrypt->verify($password, $user->getPassword())){
            return true;
        } else return false;
    }

    public function setRealName($name){
        $this->real_name = $name;
        return $this;
    }

    public function setUserName($username){
        $this->username = $username;
        return $this;
    }


    /**
     * @param mixed $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
        return $this;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
        return $this;
    }

    /**
     * @param mixed $token_validity
     */
    public function setTokenValidity($token_validity)
    {
        $this->token_validity = $token_validity;
        return $this;
    }


    public function populate($data = array()){
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->real_name = (isset($data['real_name'])) ? $data['real_name'] : null;
    }

    
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

    public function getRegistrationFilter(){
        if(!$this->registrationFilter){
            $registrationFilter = new InputFilter();
            $factory = new InputFactory();

            $email = new Input('email');
            $record_check_data = array('table' => 'user', 'field' => 'username', 'adapter' => $this->sm->get('dbAdapter') );
            $emailValidatorChain = new \Zend\Validator\ValidatorChain();
            $emailValidatorChain->attach(new Validator\EmailAddress())
                                ->attach(new \Zend\Validator\Db\NoRecordExists($record_check_data));
            $emailFilterChain = new \Zend\Filter\FilterChain();
            $emailFilterChain->attachByName('stringtrim');

            $email->setValidatorChain($emailValidatorChain)
                  ->setFilterChain($emailFilterChain);
            $registrationFilter->add($email);



            $registrationFilter->add($factory->createInput(array(
                'name' =>'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim')
                ),
            )));

            $registrationFilter->add($factory->createInput(array(
                 'name' => 'passwordCheck',
                 'required' => true,
                 'filters' => array(
                     array('name' => 'StringTrim')
                 ),
                 'validators' => array(
                     array(
                         'name' => 'Identical',
                         'options' => array(
                             'token' => 'password'
                         )
                     )
                 )
            )));

            $registrationFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 50
                        )
                    )
                )
            )));

            $this->registrationFilter = $registrationFilter;



        }

        return $this->registrationFilter;
    }

}
