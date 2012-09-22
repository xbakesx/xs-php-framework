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
                'policy' => MySQLModel::LEFT_JOIN,
                'localKey' => 'teacher_id', // matches local variable
                'foreignKey' => 'id',       // matches table column for foreign table
                'foreignModel' => 'teacher' // matches prefix of the model ('user' for UserModel)
            ),
            StudentModel::CLASS_STUDENT_MANY_TO_MANY => array(
                'relationship' => MySQLModel::MANY_TO_MANY,
                'policy' => MySQLModel::LEFT_JOIN,
                'localKey' => 'id',                    // matches local variable
                'foreignKey' => 'id',                  // matches table column for foreign table
                'joinTable' => 'student_class_assoc',  // table to join on for the many-to-many relationships
                'joinColumns' => array('year'),
                'assocLocalKey' => 'class_id',       // key joined on localKey
                'assocForeignKey' => 'student_id',     // key joined on foreignKey
                'foreignModel' => 'student'            // matches prefix of the model ('user' for UserModel)
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