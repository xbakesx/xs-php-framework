<?php

class UserModel extends DatabaseModel 
{
    private $email;
    private $password;
    private $lastLogin;
    
    function __construct()
    {
        
    }
    
    public function setLastLogin()
    {
        $this->lastLogin = date('Y-m-d h:M:s');
    }
    
    public function getEmail()
    {
        return $this->getEmail();
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }
}