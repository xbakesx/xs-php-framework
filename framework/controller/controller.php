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
    
    public function redirect($url)
    {
        header('location:'.$url);
        exit;
    }
    
    public function json($data)
    {
        echo json_encode($data);
        exit;
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
}

