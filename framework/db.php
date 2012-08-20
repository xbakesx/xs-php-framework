<?php

/**
 * This will be filled with database specific things.
 * It is loaded on every page, be frivilous.
 */
abstract class DatabaseConnection {
	protected $_host, $_username, $_password;

	public function __construct($host, $username, $password){
		$this->_host = $host;
		$this->_username = $username;
		$this->_password = $password;
	}
	
	/**
	 * @return the server to connect to for this database connection 
	 */
	public function getHost()
	{
	    return $this->_host;
	}
	
	/**
	 * @return the username for this database connection 
	 */
	public function getUsername()
	{
	    return $this->_username;
	}
	
	/**
	 * @return the password for this database connection 
	 */
	public function getPassword()
	{
	    return $this->_password;
	}
}

/**
 * Stores a MySQL database connection.
 */
class MySQLDatabaseConnection extends DatabaseConnection
{
    private $_newLink, $_clientFlags, $_databaseName;

    /**
     * @param string $host the server the database is on
     * @param string $username the user to connect with
     * @param string $password the password
     * @param string databaseName the name of the database to select
     * @param boolean $new_link true to create a new connection to the database (see mysql_connect() documentation)
     * @param number $client_flags flags to pass to mysql connection ((see mysql_connect() documentation)
     */
    public function __construct($host, $username=null, $password=null, $databaseName=null, $new_link=null, $client_flags = null)
    {
    	parent::__construct($host, $username, $password);
    	$this->_databaseName = $databaseName;
        $this->_newLink = $new_link;
        $this->_clientFlags = $client_flags;
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

