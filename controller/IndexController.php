<?php

require_once '../model/ClassModel.php';
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
    
    public function index($args)
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
CREATE TABLE `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
)

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
)

            </pre>
CREATE_TABLE;
        }
        
        return $ret;
    }
}