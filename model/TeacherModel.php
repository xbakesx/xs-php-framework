<?php

class TeacherModel extends MySQLModel 
{
    protected $name;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getTable()
    {
        return 'teacher';
    }
    
    public function getDatabaseConnectionKey()
    {
        return App::USER_DB;
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }
}