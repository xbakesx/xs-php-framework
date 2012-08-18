<?php

class UserModel extends DatabaseModel 
{
    protected $email;
    protected $password;
    protected $last_login;
    protected $auth_level;
    
    public function setLastLogin()
    {
        $this->last_login = date('Y-m-d H:i:s');
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getTable()
    {
        return 'user';
    }
    
    public function getDatabaseConnectionKey()
    {
        return App::USER_DB;
    }
}