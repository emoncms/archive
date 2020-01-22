<?php

class User
{
    private $emoncmsorg;
    
    public function __construct($emoncmsorg)
    {
        $this->emoncmsorg = $emoncmsorg;
    }
    
    //---------------------------------------------------------------------------------------
    // Status
    //---------------------------------------------------------------------------------------
    public function status()
    {
        if (!isset($_SESSION['userid'])) return false;
        if ($_SESSION['userid']<1) return false;
        return $_SESSION;
    }

    //---------------------------------------------------------------------------------------
    // User login
    //---------------------------------------------------------------------------------------
    public function register($username,$email,$password)
    {
        $result = $this->emoncmsorg->user_register($username,$email,$password);

        if (isset($result->success) && $result->success==true) {
        
            session_regenerate_id();
            $_SESSION['userid'] = $result->userid;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['apikey_read'] = $result->apikey_read;
            $_SESSION['apikey_write'] = $result->apikey_write;
            
            return $_SESSION;
        } else {
            $this->logout();
            return $result->message;
        } 
    }
    
    //---------------------------------------------------------------------------------------
    // User login
    //---------------------------------------------------------------------------------------
    public function login($username,$password)
    {    
        // Initial authentication
        $auth = $this->emoncmsorg->user_auth($username,$password);

        if (isset($auth->success) && $auth->success==true) {
            session_regenerate_id();
            // Fetch further user account information
            $user = $this->emoncmsorg->user_get($auth->apikey_write);
            $_SESSION['userid'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['email'] = $user->email;
            $_SESSION['apikey_read'] = $auth->apikey_read;
            $_SESSION['apikey_write'] = $auth->apikey_write;

        } else {
            $this->logout();
            return $auth->message;
        }    

        return $_SESSION;
    }

    //---------------------------------------------------------------------------------------
    // Logout
    //---------------------------------------------------------------------------------------
    public function logout() 
    {
        session_unset();
        session_destroy();
    }
}
