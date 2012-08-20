<?php

abstract class Model
{
    public function __construct()
    {
        // initialize all member variables to null for logic later
        $vars = get_object_vars($this);
        foreach ($vars as $key => $var)
        {
            $vars[$key] = null;
        }
    }

	public function populate(&$array, $prefix = '')
	{
	    $prefixLen = strlen($prefix);
		foreach ($array as $key => $value)
		{
		    // hack off the prefix from the key, if the key starts with the prefix
		    $keyPrefix = substr($key, 0, $prefixLen);
		    
		    if ($keyPrefix == $prefix)
		    {
		        $realKey = substr($key, $prefixLen);
    	        if (property_exists($this, $realKey))
    	        {
                    $this->$realKey = $value;
    	        }
		    }
		}
	}

	public function toArray()
	{
		return $this->getSetMemberVariables();
	}

	protected final function getSetMemberVariables()
	{
		$vars = $this->getMemberVariables();

		foreach ($vars as $i => $var)
		{
			if (is_null($var))
			{
				unset($vars[$i]);
			}
		}

		return $vars;
	}
	
	protected function getMemberVariables()
	{
		$vars = get_object_vars($this);

		// remove member vars from parent class
		unset($vars['_connection']);
		unset($vars['_queryHandle']);
		unset($vars['_connectionKey']);

		return $vars;
	}
}

interface PersistentStore
{
    /**
     * Takes a DatabaseConnection object and connects to the database.  This is called in the constructor, so careful not to use instance variables in your connect, only what is passed in.
     * @param DatabaseConnection $connection
     */
    public function connect($connection);
	/**
	 * Takes the data in the current model, and create a persistent store of it.
	 * @return the model that is persisted.
	 * @throws CreateException if the object fails to be persisted (because it already exists, or because of another failure)
	 */
	public function create();
	/**
	 * Takes the data in the current model, create a persistent store of it, or update the persistent store whichever is appropriate.
	 * @return the model that is persisted.
	 * @throws CreateException if it is appropriate to do a create and the create fails
	 * @throws UpdateException if it is appropriate to do an update and the update fails
	 */
	public function createOrUpdate();
	/**
	 * This will take teh data in the current model, and update the persistent store.
	 * @return the model that is persisted.
	 * @throws UpdateException if the objects fails to update the persistence
	 */
	public function update();
	/**
	 * Takes the data in the current model and deletes matching items from the persistence
	 * @throws DeleteException if the persistence fails to be deleted
	 */
	public function delete();

	/**
	 * Takes the parts of the current model that are set and does a search of the persistence for it.
	 * @return all the results.
	 * @throws SearchException on an error
	 */
	public function search();
}

abstract class DatabaseModel extends Model implements PersistentStore
{
	 
	public static $connectionEstablshed = array();
	private $_connectionKey;
	private $_queryHandle;
	protected $_joinArray;

	/**
	 * @param array $join an array of keys in getJoinTableAssociations()
	 */
	public function __construct($join = array())
	{
	    parent::__construct();
	    
		$this->_connectionKey = $this->getDatabaseConnectionKey();
		
		if(!isset(DatabaseModel::$connectionEstablshed[$this->_connectionKey]))
		{
			$this->connect(App::$DATABASE_CONNECTIONS[$this->_connectionKey]);
			 
			//Only ever make one connetion connection established
			DatabaseModel::$connectionEstablshed[$this->_connectionKey] = true;
		}
		
		$this->_joinArray = $join;
	}

	abstract public function getDatabaseConnectionKey();
	
	public function getJoinTableAssociations()
	{
		return array();
	}
	
	public function populate(&$array, $prefix = '')
	{
	    parent::populate($array, $prefix);
	    
	    $joinAssocs = $this->getJoinTableAssociations();
	    foreach ($joinAssocs as $key => $assoc)
	    {
	        $foreignModel = $assoc['foreignModel'];
	        if (property_exists($this, $foreignModel))
	        {
	            $foreignModelName = $foreignModel.'Model';
	            $newModel = new $foreignModelName($this->_joinArray);
	            $newModel->populate($array, $foreignModel.'_');
	            
	            $this->$foreignModel = $newModel;
	        }
	    }
	}
	
	protected function getMemberVariables()
	{
		$vars = parent::getMemberVariables();

		// remove member vars from this (the parent) class
		unset($vars['_joinArray']);

		return $vars;
	}
}

abstract class MySQLModel extends DatabaseModel
{
    private $_connection;
    
	abstract public function getTable();
    
	/**
	 * @see PersistentStore::connect()
     * @param MySQLDatabaseConnection $connection
	 */
	public function connect($connection)
	{
		if (is_null($connection) || $connection === FALSE)
		{
			throw new MySQLException('Connection Exception: No connection information supplied.');
		}

		$conn = mysql_connect($connection->getHost(), $connection->getUsername(), $connection->getPassword(), $connection->getNewLink(), $connection->getClientFlags());
		if ($conn === FALSE)
		{
		    throw new MySQLException('Connection Exception: '.mysql_error());
		}
		
		$dbName = $connection->getDatabaseName();
		if (!empty($dbName))
		{
		    $dbSelection = mysql_select_db($dbName, $conn);
		    
		    if ($dbSelection === FALSE)
		    {
		        throw new MySQLException('mysql_select_db()', 'Failed to select database: '.mysql_error($conn));
		    }
		}
		else
		{
		    throw new MySQLException('No database specified');
		}

		$this->_connection = $conn;
	}

	/**
	 * @see PersistentStore::create()
	 */
	public function create()
	{
		// takes all the member variables and does a database insert
		$props = $this->getSetMemberVariables();

		$columns = '`'.implode('`,`', array_keys($props)).'`';
		$values = "'".implode("','", $this->escapedArrayValues($props))."'";

		$this->sqlQuery("insert into `{$this->getTable()}` ($columns) values ($values)");
	}

	/**
	 * @see PersistentStore::createOrUpdate()
	 */
	public function createOrUpdate()
	{
		// takes all the member variables, checks for existence, if it exists does an update, otherwise a create
	}

	/**
	 * @see PersistentStore::update()
	 */
	public function update()
	{
		// takes all the member variables and does an database update
	}

	/**
	 * @see PersistentStore::delete()
	 */
	public function delete()
	{
		// takes the member variables and deletes matching rows in database
	}

	/**
	 * @see PersistentStore::search()
	 */
	public function search()
	{
		// takes the set member variables and returns an array of of matching rows
		$count = $this->query();

		$ret = array();
		while ($model = $this->fetch())
		{
			$ret[] = $model;
		}

		return $ret;
	}

	/**
	 * Takes the parts of the current model that are set and does a search of the persistence for it.
	 * @return the number of results
	 * @throws SearchException on an error
	 */
	public function query()
	{
		$props = $this->getSetMemberVariables();

	    $table = $this->escapeTable($this->getTable());
	    
	    $where = 'where';
	    $tables = $table;
		$columns = '';
		$columnSep = '';
		$conditions = '';
		$conditionSep = '';

	    $joinAssocs = $this->getJoinTableAssociations();	    
	    foreach ($joinAssocs as $key => $assoc)
	    { 
	        if (array_search($key, $this->_joinArray) !== FALSE)
	        {
	            unset($props[$assoc['localKey']]);
	            
	            // TODO: this is too bad, to have to create the new model, then toss it just to get the table, but I don't want to re-ask for teh table name because they already specified that once
	            $foreignModel = $assoc['foreignModel'].'Model';
	            $foreignModel = new $foreignModel();
	            $foreignTable = $this->escapeTable($foreignModel->getTable());
	            
	            $tables .= ', '.$foreignTable;
	            
	            foreach ($foreignModel->getMemberVariables() as $var => $value)
	            {
    	            $columns .= $columnSep.$foreignTable.'.'.$this->escapeColumn($var).' as '.$this->escapeColumn($foreignModel->getTable().'_'.$var);
    				$columnSep = ',';
	            }
	            
	            $conditions .= $conditionSep.$table.'.'.$this->escapeColumn($assoc['localKey']).' = '.$foreignTable.'.'.$this->escapeColumn($assoc['foreignKey']);
				$conditionSep = ' and ';
	        }
	    }
	    
	    if (empty($props))
	    {
	        $columns .= $columnSep.$table.'.*';
	    }
	    else
	    {
			foreach ($props as $col => $value)
			{
				$columns .= $columnSep.$table.'.'.$this->escapeColumn($col).' as '.$this->escapeColumn($this->getTable().'_'.$col);
				$columnSep = ',';
				$conditions .= $conditionSep.$table.'.'.$this->escapeColumn($col).' = \''.mysql_real_escape_string($value).'\'';
				$conditionSep = ' and ';
			}
	    }
	    
        if (empty($conditions))
        {
            $where = '';
        }
        
		$this->sqlQuery("select $columns from $tables $where $conditions");

		return mysql_num_rows($this->_queryHandle);
	}

	/**
	 * @return The next result from a previous query or false if there are none left
	 * @throws SearchException if there was no previous query, or an error occurred
	 */
	public function fetch()
	{
		if (is_null($this->_queryHandle))
		{
			throw new SearchException('No query has been made');
		}

		$row = mysql_fetch_assoc($this->_queryHandle);

		if ($row === FALSE)
		{
			return FALSE;
		}

		$modelName = get_class($this);
		$model = new $modelName();

		$model->populate($row);

		return $model;
	}

	public function debug()
	{
		debug($this->getSetMemberVariables());
	}
	
	protected final function getMemberVariables()
	{
		$vars = parent::getMemberVariables();

		// remove member vars from this (the parent) class
		unset($vars['_connection']);

		return $vars;
	}

	private function sqlQuery($sql)
	{
		$h = mysql_query($sql);
		
		if ($h === false)
		{
			throw new MySQLException($sql, mysql_error());
		}
		
		$this->_queryHandle = $h;
		return $h;
	}

	private function escapedArrayKeys($array)
	{
		$ret = array();
		foreach ($array as $key => $value)
		{
			$ret[] = mysql_real_escape_string($key);
		}
		return $ret;
	}

	private function escapedArrayValues($array)
	{
		$ret = array();
		foreach ($array as $key => $value)
		{
			$ret[] = mysql_real_escape_string($value);
		}
		return $ret;
	}
	
	private function escapeTable($table)
	{
	    return '`'.$table.'`';
	}
	
	private function escapeColumn($column)
	{
	    return '`'.$column.'`';
	}

}

class UpdateException extends Exception
{

}

class DeleteException extends Exception
{

}

class CreateException extends Exception
{
	const ALREADY_EXISTS = 'ALREADY_EXISTS';

	private $type;

	public function getType()
	{
		return $type;
	}
}

class SearchException extends Exception
{

}

