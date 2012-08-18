<?php

class App extends BaseApp
{
    const USER_DB = 'user_db';
    
    public function getDatabaseConnections()
    {
        return array(
            App::USER_DB => new MySQLDatabaseConnection('127.0.0.1', 'test-xs', 'vGGRCPBWVbnM4YBT', 'xs')
        );
    }
    
    public function isDebug()
    {
        return true;
    }
    
    public function getFaviconUrl()
    {
        return '';
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
        return array('bootstrap.min.css');
    }
    
    public function getJs()
    {
        return array('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
    }
}