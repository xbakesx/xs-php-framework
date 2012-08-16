<?php

interface AppInterface extends ControllerInterface
{
    /** http://www.w3schools.com/tags/tag_doctype.asp */
    /** Constants for getDoctype() */
    const HTML_VERSION_5 = '<!DOCTYPE html>';
    const HTML_VERSION_4_01_STRICT = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    const HTML_VERSION_4_01_TRANSITIONAL = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    const HTML_VERSION_4_01_FRAMESET = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
    const XHTML_VERSION_1_0_STRICT = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    const XHTML_VERSION_1_0_TRANSITIONAL = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    const XHTML_VERSION_1_0_FRAMESET = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
    const XHTML_VERSION_1_1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
    
    /** Constants for getCharSet() */
    const UTF8 = 'utf-8';
    const ISO_8859_1 = 'ISO-8859-1';
    
    public function getFaviconUrl();
    public function isDebug();
}

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
    
    public function getPreContentFile();
    public function getPostContentFile();
}

abstract class Controller implements ControllerInterface
{
    function __construct() 
    {
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
        return array();
    }
    
    public function getJs()
    {
        return array();
    }
    
    public function getPreContentFile()
    {
        return 'header.php';
    }
    
    public function getPostContentFile()
    {
        return 'footer.php';
    }
}

abstract class BaseApp implements AppInterface
{
    public function getDoctype()
    {
        return AppInterface::HTML_VERSION_5;
    }
    public function getCharSet()
    {
        return AppInterface::UTF8;
    }
    public function getPreContentFile()
    {
        return 'header.php';
    }
    public function getPostContentFile()
    {
        return 'footer.php';
    }
    public function isDebug()
    {
        return false;
    }
    
    /** These are methods that are really designed for controllers, and the app doesn't reference anyway */
    
    final public function isAuthorized()
    {
        return true;
    }
}