<?php

require_once '../model/ClassModel.php';
require_once '../model/StudentModel.php';
require_once '../model/TeacherModel.php';

class IndexController extends Controller
{
    public function isAuthorized()
    {
        return true;
        
        if(isset($_SESSION['auth'])){
    		return true;
        }
        else 
        {
        	return false;
        }
    }
    
    public function index()
    {
        
    }
    
    public function classes($args)
    {
        $ret = array();
        
        try
        {
            $classes = new ClassModel(array(ClassModel::TEACHER_ASSOC));
            $ret = $classes->search();
        }
        catch (MySQLException $ex)
        {
            $msg = $ex->getMessage();
            $ret = <<<CREATE_TABLE
            <h3>SQL Error: $msg</h3>
            <pre>

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO  `xs`.`teacher` (`id`, `name`) VALUES (NULL,  'J. Dooley'), (NULL ,  'D. Schneider');

CREATE TABLE `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `xs`.`class` (`id`, `name`, `teacher_id`) VALUES (NULL, 'Abstract Algebra', '2'), (NULL, 'Calculus III', '2'), (NULL, 'Data Structures', '1'), (NULL, 'Software Engineering', '1'), (NULL, 'Artificial Intelligence', '1');

            </pre>
CREATE_TABLE;
        }
        
        return $ret;
    }
    
    public function students($args)
    {
        $limit = array_shift($args);
        if ($limit)
        {
            $students = new StudentModel(array(), array('birthday' => '>'));
            $students->setBirthday($limit);
        }
        else
        {
            $students = new StudentModel();
        }
        $ret = array('limit' => $limit, 'students' => $students->search(new MySQLOperator('birthday', '<')));
        
        return $ret;
    }
    
    public function school($args)
    {
        $ret = array();
        
        try
        {
            $students = new StudentModel(array(StudentModel::CLASS_STUDENT_MANY_TO_MANY));
            $students->query();
            
            while ($student = $students->fetch())
            {
                $ret[] = $student;
            }
        }
        catch (MySQLException $ex)
        {
            $ret = <<<CREATE_TABLE
            
CREATE TABLE IF NOT EXISTS `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  `birthday` date NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `xs`.`student` (`id`, `name`, `birthday`) VALUES (NULL, 'Superman', '1900-01-01'), (NULL, 'Batman', '1915-04-17'), (NULL, 'The Flash', '1940-01-01'), (NULL, 'Green Lantern', '1940-07-01');

CREATE TABLE IF NOT EXISTS `student_class_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `xs`.`student_class_assoc` (`id`, `student_id`, `class_id`) VALUES (NULL, '2', '1'), (NULL, '2', '2'), (NULL, '2', '3'), (NULL, '2', '4'), (NULL, '2', '5'), (NULL, '1', '5'), (NULL, '3', '2'), (NULL, '3', '4'), ('4', '3', ''), (NULL, '4', '4')
            
CREATE_TABLE;
        }
        
        return $ret;
    }
}