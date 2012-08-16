<?php

date_default_timezone_set('Etc/GMT-6');

class App extends BaseApp
{
    public function isDebug()
    {
        return false;
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
        return array('RobotSideKick', 'xs', 'PHP Framework', 'PHP', 'Framework', 'xs php framework');
    }
    
    public function getDescription()
    {
        return 'A lightweight php framework.  It\'s got the MVC and the quick startup times and the aggressive focus on performance... as soon as I figure out what that means.';
    }
    
    public function getTitle()
    {
        return 'XS - ';
    }
    
    public function getCss()
    {
        return array('bootstrap.min.css','structure.css','common.css');
    }
    
    public function getJs()
    {
        return array('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
                     'bootstrap.min.js');
    }
}