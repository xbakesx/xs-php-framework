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
    
    /**
     * @return string a url pointing at the favicon (bookmark image) for the app 
     */
    public function getFaviconUrl();
    /**
     * @return boolean returns true if the app is in debug mode (much more verbose output, etc) make sure to set this to false for production! 
     */
    public function isDebug();
    
    /**
     * @return array a memorable string to a database connection the app will use 
     */
    public function getDatabaseConnections();
}

abstract class BaseApp implements AppInterface
{
    public static $DATABASE_CONNECTIONS = null;
    
    public function getDatabaseConnections()
    {
        return array();
    }
    
    public function getDoctype()
    {
        return AppInterface::HTML_VERSION_5;
    }
    public function getCharSet()
    {
        return AppInterface::UTF8;
    }
    public function getHeaderFile()
    {
        return 'header.php';
    }
    public function getFooterFile()
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
    
    final public function getModels()
    {
        return array();
    }
}

