<?php

class UserController extends Controller
{
    public function isAuthorized()
    {
        return true;
    }
    
    public function index()
    {
        
    }
    
    public function login()
    {
        $user = new UserModel();
        $users = $user->search();
        unset($user);
        
        return array('activeUsers' => $users);
    }
    
    public function authorize()
    {
        $ret = array();
        
        $user = new UserModel();
        $user->setEmail($_POST['email']);
        $user->setPassword($this->hashPassword($_POST['password']));
        
        if ($user->query() == 1)
        {
            try
            {
            	$user = $user->next();
                $user->setLastLogin();
                $user->update();
                $_SESSION['auth'] = $user->toArray();
                $ret = $user;
            }
            catch (UpdateException $ex)
            {
                $ret = array('error' => $ex->getMessage());
            }
        }
        else
        {
            $ret = array('error' => 'Invalid email and password');
        }
        
        return $this->json($ret);
    }
    
    public function logout()
    {
        session_destroy();
        header('location: /user/login');
        exit;
    }
    
    public function create()
    {
        $email = $_POST['email'];
        $password = $this->hashPassword($_POST['password']);
        
        $user = new UserModel();
        $user->setEmail($email);
        $user->setPassword($password);
        
        try
        {
            $user->create();
            
            return $this->authorize();
        }
        catch (CreateException $ex)
        {
            if ($ex->getType() == CreateException::ALREADY_EXISTS)
            {
                return $this->authorize();
            }
            else
            {
                return $this->json(array('error' => $ex->getMessage()));
            }
        }
    }
    
    public function changeEmail()
    {
        $oldEmail = $_POST['oldEmail'];
        $newEmail = $_POST['newEmail'];
        
        $userSearch = new UserModel();
        $userSearch->setEmail($newEmail);
        
        $foundUsers = $userSearch->search();
        if (count($foundUsers) > 0)
        {
            $this->json(array('error' => 'A user with that email already exists'));
        }
        
        $userSearch = new UserModel();
        $userSearch->setEmail($oldEmail);
        
        $foundUser = array_shift($userSearch->search());
        $foundUser->setEmail($newEmail);
        $foundUser->update();

        $this->json($foundUser);
    }
    
    public function resetPassword()
    {
        $oldPassword = $this->hashPassword($_POST['oldPassword']);
        $newPassword = $this->hashPassword($_Post['newPassword']);
        
        $email = $_SESSION['auth']->getEmail();
        
        $user = new UserModel();
        $user->setEmail($email);
        $user->setPassword($oldPassword);
        $user = array_shift($user->search());

        $user->setPassword($newPassword);
        $user->update();
        
        return $this->json($user);
    }
    
    /**
     * @todo implement this with crypt and a persisted salt per user
     * @param string $password password to hash
     * @return a one-way hash representing the password
     */
    private function hashPassword($password)
    {
        return sha1($password);
    }
}