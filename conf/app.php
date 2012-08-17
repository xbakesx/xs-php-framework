<?php

class App extends BaseApp
{
    const USER_DB = 'user_db';
    
    public function getDatabaseConnections()
    {
        return array(
            App::USER_DB => new MySQLDatabaseConnection('127.0.0.1', 'root', '', 'xs')
        );
    }
    
    public function isDebug()
    {
        return true;
    }
    
    public function getFaviconUrl()
    {
        return 'http://www.robotsidekick.com/images/butler_favicon.png';
    }
    
    public function getAuthor()
    {
        return 'RobotSideKick';
    }
    
    public function getKeywords()
    {
        return array();
    }
    
    public function getDescription()
    {
        return '';
    }
    
    public function getTitle()
    {
        return 'XS - ';
    }
    
    public function getCss()
    {
        return array('structure.css','common.css');
    }
    
    public function getJs()
    {
        return array();
    }
}