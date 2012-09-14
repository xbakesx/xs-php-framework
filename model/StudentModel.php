<?php

class StudentModel extends MySQLModel 
{
    const CLASS_STUDENT_MANY_TO_MANY = 0;
    
    protected $id;
    protected $name;
    protected $birthday;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
    
    public function getClasses()
    {
        return $this->getJoinData('ClassModel');
    }
    
    public function getTable()
    {
        return 'student';
    }
    
    public function getJoinTableAssociations()
    {
        return array(
            StudentModel::CLASS_STUDENT_MANY_TO_MANY => array(
                'relationship' => MySQLModel::MANY_TO_MANY,
                'policy' => MySQLModel::LEFT_JOIN,
                'localKey' => 'id',                    // matches local variable
                'foreignKey' => 'id',                  // matches table column for foreign table
                'joinTable' => 'student_class_assoc',  // table to join on for the many-to-many relationships
                'assocLocalKey' => 'student_id',       // key joined on localKey
                'assocForeignKey' => 'class_id',     // key joined on foreignKey
                'foreignModel' => 'class'            // matches prefix of the model ('user' for UserModel)
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