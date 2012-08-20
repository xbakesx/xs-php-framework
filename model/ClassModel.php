<?php

class ClassModel extends MySQLModel 
{
    const TEACHER_ASSOC = 'teacher';
    
    protected $name;
    
    /**
     * @var TeacherModel $teacher
     */
    protected $teacher;
    protected $teacher_id;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setTeacherForeignKey($id)
    {
        $this->teacherForeignKey = $id;
    }
    
    public function setTeacher($teacher)
    { 
        $this->teacher = $teacher;
    }
    
    public function getTeacher()
    {
        return $this->teacher;
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
}