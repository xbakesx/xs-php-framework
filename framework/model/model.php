<?php

interface PersistentStore
{
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

/**
 * @author alex
 *
 */
abstract class DatabaseModel implements PersistentStore
{
	 
	public static $connectionEstablshed = array();
	protected $_connection;
	private $_connectionKey;
	private $_queryHandle;

	/**
	 * @param MySQLDatabaseConnection $dbConnection
	 */
	public function __construct()
	{
		// initialize all member variables to null for logic later
		$vars = get_object_vars($this);
		foreach ($vars as $key => $var)
		{
			$vars[$key] = null;
		}
		$this->_connectionKey = $this->getDatabaseConnectionKey();

		if(!isset(DatabaseModel::$connectionEstablshed[$this->_connectionKey])){
			$this->_connection = App::$DATABASE_CONNECTIONS[$this->_connectionKey];
			$this->connect();
			 
			//Only ever make one connetion connection established
			DatabaseModel::$connectionEstablshed[$this->_connectionKey] = true;
		}
	}

	private function connect()
	{
		if (is_null($this->_connection) || $this->_connection === FALSE)
		{
			throw new MySQLException('Connection Exception: No connection information supplied.');
		}

		if (is_a($this->_connection, 'MySQLDatabaseConnection'))
		{
			$conn = mysql_connect($this->_connection->getHost(), $this->_connection->getUsername(), $this->_connection->getPassword(), $this->_connection->getNewLink(), $this->_connection->getClientFlags());
			if ($conn === FALSE)
			{
				throw new MySQLException('Connection Exception: '.mysql_error());
			}

			$dbName = $this->_connection->getDatabaseName();
			if (!empty($dbName))
			{
				mysql_select_db($dbName, $conn);
			}

			$this->_connection = $conn;
		}
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
		while ($model = $this->next())
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

		if (empty($props))
		{
			$columns = '*';
			$conditions = '1=1';
		}
		else
		{
			$columns = '';
			$columnSep = '';
			$conditions = '';
			$conditionSep = '';
			foreach ($props as $col => $value)
			{
				$columns .= $columnSep.$col;
				$columnSep = ',';
				$conditions .= $conditionSep.$col.' = \''.mysql_real_escape_string($value).'\'';
				$conditionSep = ' and ';
			}
		}

		$this->sqlQuery("select $columns from {$this->getTable()} where $conditions");

		return mysql_num_rows($this->_queryHandle);
	}

	/**
	 * @return The next result from a previous query or false if there are none left
	 * @throws SearchException if there was no previous query, or an error occurred
	 */
	public function next()
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

	abstract public function getTable();
	abstract public function getDatabaseConnectionKey();

	public function getJoinTableAssociations()
	{
		return array();
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

	public function populate($array)
	{
		foreach ($array as $key => $value)
		{
			$this->$key = $value;
		}
	}

	public function toArray()
	{
		return $this->getSetMemberVariables();
	}

	private function getSetMemberVariables()
	{
		$vars = get_object_vars($this);

		// remove member vars from parent class
		unset($vars['_connection']);
		unset($vars['_queryHandle']);
		unset($vars['_connectionKey']);

		foreach ($vars as $i => $var)
		{
			if (is_null($var))
			{
				unset($vars[$i]);
			}
		}

		return $vars;
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

	public function debug()
	{
		debug($this->getSetMemberVariables());
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

