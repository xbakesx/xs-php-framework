<?php

class ComponentController extends Controller
{
    public function isAuthorized()
    {
        return true;
    }
    
    public function index($args)
    {
        return FALSE;
    }
}

?>