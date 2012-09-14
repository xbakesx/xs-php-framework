<?php

class ClassModel extends MySQLModel 
{
    const TEACHER_ASSOC = 'teacher';
    
    protected $name;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getTeacher()
    {
        $teachers = $this->getJoinData('TeacherModel');
        return array_shift($teachers);
    }
    
    public function getTable()
    {
        return 'class';
    }
    
    public function getJoinTableAssociations()
    {
        return array(
            ClassModel::TEACHER_ASSOC => array(
                'localKey' => 'teacher_id', // matches local variable
                'foreignKey' => 'id',       // matches table column for foreign table
                'foreignModel' => 'teacher' // matches prefix of the model ('user' for UserModel)
            )
        );
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