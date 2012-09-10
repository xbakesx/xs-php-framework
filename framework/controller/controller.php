<?php

interface ControllerInterface 
{
    public function isAuthorized();
    
    public function getDoctype();
    public function getCharSet();
    public function getTitle();
    public function getAuthor();
    public function getKeywords();
    public function getDescription();
    public function getCss();
    public function getJs();
    
    public function getHeaderFile();
    public function getFooterFile();
    public function getModels();
}

abstract class Controller implements ControllerInterface
{
    public $app;
    
    /**
     * @param App $app an instance of the application config
     */
    function __construct($app) 
    {
        $this->app = $app;
    }
    
    /**
     * Redirects the user to the url given then exits.
     * @param string $url url to redirect to
     */
    public function redirect($url)
    {
        header('location:'.$url);
        exit;
    }
    
    /**
     * This function outputs the given argument as json, prints it out and exits.  
     * @param mixed $data any php object that can be encoded into json by json_encode
     */
    public function json($data)
    {
        echo json_encode($data);
        exit;
    }
    
    /**
     * @param string $controller the name of the controller ('IndexController')
     * @return Controller an instance of the controller name passed in
     */
    public function includeController($controller)
    {
        return includeController($controller, '../controller/'.$controller.'.php', $this->app);
    }
    
    /**
     * @param string $method the name of the view you'd like to get
     * @return string the name of the view file
     */
    public function getViewFile($view = FALSE)
    {
        if ($method === FALSE)
        {
            $trace = array_shift(debug_backtrace());
            $view = $trace['function'];
        }
        return "../view/$view.php";
    }
    
    protected function getDatabaseConnection($key)
    {
        $dbs = $this->app->getDatabaseConnections();
        return $dbs[$key];
    }
    
    public function getDoctype()
    {
        return '';
    }
    
    public function getCharSet()
    {
        return '';
    }
    
    public function getTitle()
    {
        return '';
    }
    
    public function getAuthor()
    {
        return '';
    }
    
    public function getKeywords()
    {
        return array();
    }
    
    public function getDescription()
    {
        return '';
    }
    
    public function getCss()
    {
        return FALSE;
    }
    
    public function getJs()
    {
        return FALSE;
    }
    
    public function getHeaderFile()
    {
        return 'header.php';
    }
    
    public function getFooterFile()
    {
        return 'footer.php';
    }
    
    public function getModels()
    {
        return array();
    }
    
    public function getComponents(){
    	return array();
    }
}

