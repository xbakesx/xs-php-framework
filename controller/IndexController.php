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
        else {
        	return false;
        }
    }
    
    public function requiresAuthorization(){
    	return true;
    }
    
    public function index($args)
    {
        $ret = array();
        
        $classes = new ClassModel(array(ClassModel::TEACHER_ASSOC));
        
        $ret = $classes->search();
        return $ret;
    }
}