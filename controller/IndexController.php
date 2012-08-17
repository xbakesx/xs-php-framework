<?php

class IndexController extends Controller
{
    public function isAuthorized()
    {
        if(isset($_SESSION['auth'])){
    		return true;
        }
        else {
        	return false;
        }
    }
    
    public function index($args)
    {
        return null;
    }
}