<?php

/**
 * This will be filled with database specific things.
 * It is loaded on every page, be frivilous.
 */

/**
 * Stores a MySQL database connection.
 */
class MySQLDatabaseConnection
{
    private $host, $username, $password, $new_link, $client_flags;
    private $databaseName;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param boolean $new_link
     * @param number $client_flags
     */
    public function __construct($host, $username=null, $password=null, $databaseName=null, $new_link=null, $client_flags = null)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->new_link = $new_link;
        $this->client_flags = $client_flags;
        
        $this->databaseName = $databaseName;
    }

    /**
     * @return string the host
     */
    public function getHost() {
        return $this->host;
    }
    /**
     * @return string the username
     */
    public function getUsername() {
        return $this->username;
    }
    /**
     * @return string the password
     */
    public function getPassword() {
        return $this->password;
    }
    /**
     * @return boolean see mysql_connect for behavior
     */
    public function getNewLink() {
        return $this->new_link;
    }
    /**
     * @return number flags
     */
    public function getClientFlags() {
        return $this->client_flags;
    }
    
    /**
     * @return string the name of the database to connect to 
     */
    public function getDatabaseName() {
        return $this->databaseName;
    }

}

class MySQLException extends Exception
{
    private $query;
    
    public function __construct($query, $error)
    {
        parent::__construct($error);
        $this->query = $query;
    }
    
    public function debug()
    {
        debug($this->getMessage());
        debug($this->query);
        debug($this->getTrace());
    }
}

