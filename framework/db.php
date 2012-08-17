<?php

/**
 * This will be filled with database specific things.
 * It is loaded on every page, be frivilous.
 */
abstract class DatabaseConnection {
	protected $_host, $_username, $_password, $_databaseName;

	public function __construct($host, $username, $password, $database){
		$this->_host = $host;
		$this->_username = $username;
		$this->_password = $password;
		$this->_databaseName = $database;
	}
}
/**
 * Stores a MySQL database connection.
 */
class MySQLDatabaseConnection extends DatabaseConnection
{
    private $_newLink, $_clientFlags;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param boolean $new_link
     * @param number $client_flags
     */
    public function __construct($host, $username=null, $password=null, $databaseName=null, $new_link=null, $client_flags = null)
    {
    	parent::__construct($host, $username, $password, $databaseName);
        $this->_newLink = $new_link;
        $this->_clientFlags = $client_flags;
    }

    /**
     * @return string the host
     */
    public function getHost() {
        return $this->_host;
    }
    /**
     * @return string the username
     */
    public function getUsername() {
        return $this->_username;
    }
    /**
     * @return string the password
     */
    public function getPassword() {
        return $this->_password;
    }
    /**
     * @return boolean see mysql_connect for behavior
     */
    public function getNewLink() {
        return $this->_newLink;
    }
    /**
     * @return number flags
     */
    public function getClientFlags() {
        return $this->_clientFlags;
    }
    
    /**
     * @return string the name of the database to connect to 
     */
    public function getDatabaseName() {
        return $this->_databaseName;
    }

}

class MySQLException extends Exception
{
    private $_query;
    
    public function __construct($query, $error)
    {
        parent::__construct($error);
        $this->_query = $query;
    }
    
    public function debug()
    {
        debug($this->getMessage());
        debug($this->query);
        debug($this->getTrace());
    }
}

