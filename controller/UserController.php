<?php

class UserController extends Controller
{
    public function isAuthorized()
    {
        return true;
    }
    
    public function authorize($args)
    {
        $this->json($args);
    }
    
    public function login($args)
    {
    }
    
    public function logout()
    {
        session_destroy();
        header('location: /user/login');
        exit;
    }
}