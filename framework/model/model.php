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
	protected $_joinArray;
	protected $_operators;

	/**
	 * @param array $join an array of keys in getJoinTableAssociations()
	 * @param array $operators an array of table columns to operators
	 */
	public function __construct($join = array(), $operators = array())
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
		$this->_operators = $operators;
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
		unset($vars['_operators']);

		return $vars;
	}
}

abstract class MySQLModel extends DatabaseModel
{
    const MANY_TO_MANY = 1;
    const ONE_TO_MANY = 2;
    
    private $_connection;
	private $_queryHandle;
	private $_query;
    
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
	public function search($listOfSpecialClause = FALSE)
	{
		// takes the set member variables and returns an array of of matching rows
		$count = $this->query($listOfSpecialClause);

		$ret = array();
		while ($model = $this->fetch())
		{
			$ret[] = $model;
		}

		return $ret;
	}

	/**
	 * Takes the parts of the current model that are set and does a search of the persistence for it.
	 * @param array $listOfSpecialClause this is an array of MySQLCondition
	 * @return the number of results
	 * @throws SearchException on an error
	 */
	public function query($listOfSpecialClause = array())
	{
		$props = $this->getSetMemberVariables();
	    $table = $this->escapeTable($this->getTable());
	    
	    $where = 'where';
	    $tables = $table;
		$columns = '';
		$columnSep = '';
		$conditions = '';
		$conditionSep = '';
		$special = '';    // order by, group by, limit
		
		// ensure $listOfSpecialClause is an array
		if (!is_array($listOfSpecialClause))
		{
		    $listOfSpecialClause = array($listOfSpecialClause);
		}

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
	            
	            $columns .= $columnSep.$foreignTable.'.*';
	            $columnSep = ',';
	            
	            if (isset($assoc['relationship']) && $assoc['relationship'] === MySQLModel::MANY_TO_MANY)
	            {
	                $joinTable = $this->escapeTable($assoc['joinTable']);
	                $tables .= ', '.$this->escapeTable($assoc['joinTable']).', '.$foreignTable;
	                
	                $conditions .= $conditionSep.$table.'.'.$this->escapeColumn($assoc['localKey']).' = '.$joinTable.'.'.$this->escapeColumn($assoc['assocLocalKey']);
	                $conditionSep = ' and ';
    	            
    	            $conditions .= $conditionSep.$joinTable.'.'.$this->escapeColumn($assoc['assocForeignKey']).' = '.$foreignTable.'.'.$this->escapeColumn($assoc['foreignKey']);
    				$conditionSep = ' and ';
	            }
	            else
	            {
    	            $tables .= ', '.$foreignTable;
    	            
    	            $conditions .= $conditionSep.$table.'.'.$this->escapeColumn($assoc['localKey']).' = '.$foreignTable.'.'.$this->escapeColumn($assoc['foreignKey']);
    				$conditionSep = ' and ';
	            }
	        }
	    }
	    
	    $columns .= $columnSep.$table.'.*';
	    
	    if (!empty($props))
	    {
	        $operators = $this->getOperators($listOfSpecialClause);
	    
			foreach ($props as $col => $value)
			{
			    $op = '=';
			    if (isset($operators[$col]))
			    {
			        $op = $operators[$col];
			    }
				$conditions .= $conditionSep.$table.'.'.$this->escapeColumn($col).' '.$op.' \''.mysql_real_escape_string($value).'\'';
				$conditionSep = ' and ';
			}
	    }
	    
	    if($listOfSpecialClause)
	    {
	    	$special = $this->handleSpecialClauses($listOfSpecialClause);
	    }
	    
        if (empty($conditions))
        {
            $where = '';
        }
        
		$this->sqlQuery("select $columns from $tables $where $conditions $special");
		
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
	
	/**
	 * @return the sql query that was used in the last call to query() or search()
	 */
	public final function getLastQuery()
	{
	    return $this->_query;
	}

	private function sqlQuery($sql)
	{
		$h = mysql_query($sql);
		
		if ($h === false)
		{
			throw new MySQLException($sql, mysql_error());
		}
		
		$this->_queryHandle = $h;
		$this->_query = $sql;
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
	
	private function getOperators($specialClauses)
	{
	    $ret = array();
	    foreach ($specialClauses as $clause)
	    {
	        if ($clause instanceof MySQLOperator)
	        {
	            $ret[$clause->getColumn()] = $clause->getOperator();
	        }
	    }
	    return $ret;
	}
	
	private function handleSpecialClauses($specialClauses)
	{
		$keywordItems = array();
		foreach($specialClauses as $clause)
		{
		    if ($clause instanceof MySQLSpecialClause)
		    {
				/* @var $clause MySQLSpecialClause */
				if(!isset($keywordItems[$clause->getKeyword()]))
				{
					$keywordItems[$clause->getKeyword()]=array();
				}
				$keywordItems[$clause->getKeyword()][] = $clause->getData().',';
		    }
		}
		
		$strRet = '';
		if(isset($keywordItems['group by'])){
			$arrayItem['group by'] = $keywordItems['group by'];
			$strRet .= MySQLSpecialClause::buildCondition($arrayItem);
			unset($keywordItems['group by']);
			$arrayItem=array();
		}
		
		if(isset($keywordItems['order by'])){
			$arrayItem['order by'] = $keywordItems['order by'];
			$strRet .= MySQLSpecialClause::buildCondition($arrayItem);
			unset($keywordItems['order by']);
			$arrayItem=array();
		}
		
		if(isset($keywordItems['limit'])){
			$arrayItem['limit'] = $keywordItems['limit'];
			$strRet .= MySQLSpecialClause::buildCondition($arrayItem);
			unset($keywordItems['limit']);
		}
		
		
		return $strRet;
	}

}

interface MySQLCondition {
    
} 

class MySQLSpecialClause implements MySQLCondition 
{
	private $_keyword;
	private $_data = array();

	public function __construct($keyword, $data = FALSE){
		$this->_keyword=$keyword;
		if ($data !== FALSE)
		{
		    $this->_data[] = $data;
		}
	}

	public static function buildCondition($bundledConditions)
	{
		$returnString = ' ';
		foreach($bundledConditions as $keyword =>$value){
			$returnString.=' '.$keyword.' ';
			if(is_array($value)){
				//This is only currently for Order By.
				foreach($value as $string){
					$returnString.=$string;
				}
			}
			else {
				$returnString .= $value.'';
			}
			$returnString = substr($returnString, 0,-1);
		}
		return ($returnString);
	}

	public function getKeyword()
	{
		return trim(strtolower($this->_keyword));
	}

	protected function addAdditionalData($data)
	{
		$this->_data[] = $data;
	}

	public function getData()
	{
		$data = $this->formatDataElement($this->_data);
		//Handles for limit which only has one value following it.
		if(strlen($data)>1){
			$data= substr($data, 0,-1);
		}
		return $data;
	}

	private function formatDataElement($dataElement)
	{
		$stringToReturn = '';
		foreach($dataElement as $value){
			if(is_array($value)){
				$stringToReturn .= $this->formatDataElement($value).',';
			}
			else {
				$stringToReturn.=' '.$value.'';
			}
		}
		return trim($stringToReturn);
	}
}

class MySQLOrderBy extends MySQLSpecialClause {
	const Ascending = 'ASC';
	const Descending = 'DESC';

	public function __construct($column, $order){
		parent::__construct('Order By ', array($column, $order));
	}
}

class MySQLGroupBy extends MySQLSpecialClause {
	public function __construct($column){
		parent::__construct('Group By ', $column);
	}
}

class MySQLLimit extends MySQLSpecialClause {
	public function __construct($amount){
		parent::__construct('Limit', $amount);
	}
}

class MySQLOperator implements MySQLCondition {
    
    private $column;
    private $operator;
    
    public function __construct($column, $operator)
    {
        $this->column = $column;
        $this->operator = $operator;
    }
    
    public function getColumn()
    {
        return $this->column;
    }
    
    public function getOperator()
    {
        return $this->operator;
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

