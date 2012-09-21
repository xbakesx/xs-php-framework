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
	    $count = 0;
		foreach ($array as $key => $value)
		{
		    if (property_exists($this, $key))
		    {
		        ++$count;
		        if (is_array($value))
		        {
		            $value = array_shift($value);
		        }
		        $this->$key = $value;
		    }
		}

		return $count;
	}
	
	protected function createNewModel($data)
	{
	    $modelName = get_class($this);
	    $model = new $modelName();
	    
	    $model->populate($data);
	    
	    return $model;
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
	 * @param bool $singleRow if true, thows an exception when trying to affect more than one row
	 * @return the model that is persisted.
	 * @throws UpdateException if the objects fails to update the persistence
	 */
	public function update();
	/**
	 * Takes the data in the current model and deletes matching items from the persistence
	 * @param bool $singleRow if true, thows an exception when trying to affect more than one row
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
	protected $_primaryKey;
	protected $_joinData;

	public function __construct()
	{
	    parent::__construct();
	    
		$this->_connectionKey = $this->getDatabaseConnectionKey();
		
		if(!isset(DatabaseModel::$connectionEstablshed[$this->_connectionKey]))
		{
			$this->connect(App::$DATABASE_CONNECTIONS[$this->_connectionKey]);
			 
			//Only ever make one connetion connection established
			DatabaseModel::$connectionEstablshed[$this->_connectionKey] = true;
		}
		
		$this->_joinArray = array();
		$this->_joinData = array();
	}

	/**
	 * @return mixed a key to an element in the return of App->getDatabaseConnections()  
	 */
	abstract public function getDatabaseConnectionKey();
	/**
	 * @return string the name of the column that will be used as the primary key.  The primary key
	 * will be used to uniquely identify an item.
	 */
	abstract public function getPrimaryKey();
	/**
	 * @param string the name of a model to get joined entries on
	 * @return array an array populated with models which is the result of joins on other models (tables)
	 */
	public function getJoinData($model = FALSE)
	{
	    if ($model !== FALSE)
	    {
	        return $this->_joinData[$this->getJoinDataKey($model)];
	    }
	    else
	    {
	        return $this->_joinData;
	    }
	}
	
	/**
     * @return array the return of this defines how your model is connected to other models
     * <p><blockquote><pre>
     * MANY TO MANY EXAMPLE:
     * table 1: student (id, name)
     * table 2: class (id, name)
     * pivot: student_class_assoc (id, class_id, student_id)
     * 
     * Your Array in the StudentModel:
     * return array(
     *     YOUR_KEY_TO_IDENTIFY_THIS_ASSOCIATION => array(
     *          'relationship' => MySQLModel::MANY_TO_MANY, // this is a many-to-many relationship
     *          'policy' => MySQLModel::LEFT_JOIN          // does left joins (vs inner join)
     *          'localKey' => 'student_id',                 // refers to student.id
     *          'foreignKey' => 'class_id',                 // refers to class.id
     *          'joinTable' => 'student_class_assoc',       // this is your pivot table
     *          'assocLocalKey' => 'student_id',            // refers to student_class_assoc.student_id
     *          'assocForeignKey' => 'class_id',            // refers to student_class_assoc.class_id
     *          'foreignModel' => 'class'                   // matches prefix of the model for the second table (table 2, class) 'class' -> 'ClassModel'
     *     )
     * );
     * 
     * ONE TO MANY EXAMPLE:
     * table 1: class (id, name, teacher_id)
     * table 2: teacher(id, first_name, last_name)
     * 
     * Your Array in the ClassModel:
     *  return array(
     *      YOUR_KEY_TO_IDENTIFY_THIS_ASSOCIATION => array(
     *          'policy' => MySQLModel::INNER_JOIN          // does inner joins (vs left join)
     *          'localKey' => 'teacher_id',                 // refers to class.teacher_id
     *          'foreignKey' => 'id',                       // refers to teacher.id
     *          'foreignModel' => 'teacher'                 // matches prefix of the model for the second table (table 2, teacher) 'teacher' -> 'TeacherModel'
     *      )
     *  );
     * </pre></blockquote></p>
	 */
	public function getJoinTableAssociations()
	{
		return array();
	}
	
	/**
	 * @param array $joinId a key in getJoinTableAssociations() to do joins against
	 */
	public function addJoin($joinId)
	{
	    $this->_joinArray[] = $joinId;
	}
	
	/**
	 * @param array $joinIds an array of keys in getJoinTableAssociations() to do joins against
	 */
	public function setJoins($joinIds)
	{
	    if (!is_array($joinIds))
	    {
	        $joinIds = array($joinIds);
	    }
	    
	    $this->_joinArray = $joinIds;
	}
	
	public function populate(&$array, $prefix = '')
	{
	    $count = parent::populate($array, $prefix);
	    
	    $joinAssocs = $this->getJoinTableAssociations();
	    foreach ($joinAssocs as $key => $assoc)
	    {
	        if (array_search($key, $this->_joinArray) !== FALSE)
	        {
    	        $foreignModel = $assoc['foreignModel'];
    	        $foreignModelName = $foreignModel.'Model';
    	        $modelData = $this->extractData($array, $foreignModel.'_');
    	        
    	        $isMultipleModels = $modelData['isMultipleModels'];
    	        unset($modelData['isMultipleModels']);
    	        if ($isMultipleModels)
    	        {
    	            foreach ($modelData as $values)
    	            {
    	                $newModel = new $foreignModelName();
    	                $subcount = $newModel->populate($values);
            	        if ($subcount > 0)
            	        {
            	            $key = $this->getJoinDataKey($foreignModelName);
            	            if (!isset($this->_joinData[$key]))
            	            {
            	                $this->_joinData[$key] = array();
            	            }
            	            $pkey = $newModel->getPrimaryKey();
            	            $this->_joinData[$key][$newModel->$pkey] = $newModel;
            	            $count += $subcount;
            	        }
    	            }
    	        }
    	        else
    	        {
    	            $newModel = new $foreignModelName();
    	            $subcount = $newModel->populate($modelData);
        	        if ($subcount > 0)
        	        {
        	            $key = $this->getJoinDataKey($foreignModelName);
        	            if (!isset($this->_joinData[$key]))
        	            {
        	                $this->_joinData[$key] = array();
        	            }
        	            $pkey = $newModel->getPrimaryKey();
        	            $this->_joinData[$key][$newModel->$pkey] = $newModel;
        	            $count += $subcount;
        	        }
    	        }
    	         
	        }
	    }
	    return $count;
	}
	
	private function extractData($array, $prefix)
	{
	    $ret = array();
	    $isMultipleModels = false;
	    $prefixLen = strlen($prefix);
	    foreach ($array as $key => $value)
	    {
	        if (substr($key, 0, $prefixLen) == $prefix)
	        {
	            $newKey = substr($key, $prefixLen);
	            if (is_array($value))
	            {
	                $isMultipleModels = true;
	                $tmp = array();
	                foreach ($value as $oneValue)
	                {
	                    $tmp[] = $oneValue;
	                }
	                $ret[$newKey] = $tmp;
	            }
	            else
	            {
	                $ret[$newKey] = $value;
	            }
	        }
	    }
	    // now "invert" array for easier navigation later
	    if ($isMultipleModels)
	    {
	        $newRet = array();
	        foreach ($ret as $key => $values)
	        {
	            foreach ($values as $i => $value)
	            {
	                if (!is_null($value))
	                {
	                    $newRet[$i][$key] = $value;
	                } 
	            }
	        }
	        $ret = $newRet;
	    }
	    $ret['isMultipleModels'] = $isMultipleModels;
	    
	    return $ret;
	}
	
	public function getJoinDataKey($model)
	{
	    return strtolower($model);
	}

	public function toArray()
	{
		$ret = parent::toArray();
		$this->toArrayFromObject($ret, 'joinData', $this->_joinData);
		return $ret;
	}
	
	private function toArrayFromObject(&$array, $key, $value)
	{
	    if (is_object($value))
	    {
	        $array[$key] = $value->toArray();
	    }
	    else 
	    {
	        $tmp = $value;
    	    if (is_array($value))
    	    {
    	        foreach ($value as $k => $v)
    	        {
    	            $this->toArrayFromObject($tmp, $k, $v);
    	        }
    	    }
    	    
    	    $array[$key] = $tmp;
	    }
	}
	
	protected function getMemberVariables()
	{
		$vars = parent::getMemberVariables();

		// remove member vars from this (the parent) class
		unset($vars['_joinArray']);
		unset($vars['_primaryKey']);
		unset($vars['_joinData']);

		return $vars;
	}
}

abstract class MySQLModel extends DatabaseModel
{
    const MANY_TO_MANY = 1;
    const ONE_TO_MANY = 2;
    
    const LEFT_JOIN = 1;
    const INNER_JOIN = 2;
    
    private $_connection;
	private $_queryHandle;
	private $_query;
	
	/** Used for fetchObject() */
	private $_fetchObjectBuffer;
	
	public function __construct()
	{
	    parent::__construct();
	    
	    $this->_fetchObjectBuffer = false;
	}
    
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

		$this->sqlQuery("insert into {$this->escapeTable($this->getTable())} ($columns) values ($values)");
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
	    $props = $this->getSetMemberVariables();
	    $primaryKeyColumn = $this->getPrimaryKey();

	    if (empty($props[$primaryKeyColumn]))
	    {
	        throw new MySQLException('Model->update()', 'Make sure your model has an instance variable of your Primary Key.');
	    }

	    $primaryKeyValue = mysql_real_escape_string($props[$primaryKeyColumn]);
	    unset($props[$primaryKeyColumn]);

	    $set = '';
	    $sep = ' SET ';
	    foreach ($props as $key => $value)
	    {
	        $set .= $sep.$this->escapeColumn($key).' = '.$this->escapeValue($value);
	        $sep = ', ';
	    }

	    return $this->sqlQuery("update {$this->escapeTable($this->getTable())} $set WHERE {$this->escapeColumn($primaryKeyColumn)} = {$this->escapeValue($primaryKeyValue)}");
	}

	/**
	 * @see PersistentStore::delete()
	 */
	public function delete($listOfSpecialClause = array())
	{
	    $props = $this->getSetMemberVariables();
	    $where = '';
	    if (!empty($props))
	    {
	        $table = $this->escapeTable($this->getTable());
	        
	        // ensure $listOfSpecialClause is an array
	        if (!is_array($listOfSpecialClause))
	        {
	            $listOfSpecialClause = array($listOfSpecialClause);
	        }
	        
	        $where = ' WHERE '.$this->getWhereClause($props, $table, $listOfSpecialClause);
	    }
		$this->sqlQuery("delete from {$this->escapeTable($this->getTable())}$where");
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
	 * This returns an array of fetchObject() calls emptying your query results
	 * @see PersistentStore::search()
	 */
	public function searchObjects($listOfSpecialClause = FALSE)
	{
		// takes the set member variables and returns an array of of matching rows
		$count = $this->query($listOfSpecialClause);

		$ret = array();
		while ($model = $this->fetchObject())
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
	    
	    $where = 'where ';
	    $tables = $table;
		$columns = '';
		$columnSep = '';
		$conditions = '';
		$conditionSep = '';
		$special = '';    // order by, group by, limit
		
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
	            includeElement(array($foreignModel.'.php'), 'model');
	            $foreignModel = new $foreignModel();
	            $foreignTable = $this->escapeTable($foreignModel->getTable());
	            
	            $columns .= $this->getAllColumnSql($foreignModel, $columnSep, $assoc['foreignModel']);
	            $columnSep = ',';
	            
	            if (isset($assoc['relationship']) && $assoc['relationship'] === MySQLModel::MANY_TO_MANY)
	            {
	                $joinTable = $this->escapeTable($assoc['joinTable']);
    	                
	                $cond1 = $table.'.'.$this->escapeColumn($assoc['localKey']).' = '.$joinTable.'.'.$this->escapeColumn($assoc['assocLocalKey']);
	                $cond2 = $joinTable.'.'.$this->escapeColumn($assoc['assocForeignKey']).' = '.$foreignTable.'.'.$this->escapeColumn($assoc['foreignKey']);
	                
	                if (isset($assoc['policy']) && $assoc['policy'] == MySQLModel::LEFT_JOIN)
	                {
    	                $tables .= ' left join '.$this->escapeTable($assoc['joinTable']).' on '.$cond1.' left join '.$foreignTable.' on '.$cond2;
	                }
	                else
	                {
    	                $tables .= ', '.$this->escapeTable($assoc['joinTable']).', '.$foreignTable;
    	                
    	                $conditions .= $conditionSep.$cond1;
    	                $conditionSep = ' and ';
        	            
        	            $conditions .= $conditionSep.$cond2;
        	            $conditionSep = ' and ';
	                }
	            }
	            else
	            {
	                $cond = $table.'.'.$this->escapeColumn($assoc['localKey']).' = '.$foreignTable.'.'.$this->escapeColumn($assoc['foreignKey']);
	                
	                if (isset($assoc['policy']) && $assoc['policy'] == MySQLModel::LEFT_JOIN)
	                {
	                    $tables .= ' left join '.$foreignTable.' on '.$cond;
	                }
	                else
	                {
        	            $tables .= ', '.$foreignTable;
        	            
        	            $conditions .= $conditionSep.$cond;
        				$conditionSep = ' and ';
	                }
	            }
	        }
	    }
	    
	    $columns .= $this->getAllColumnSql($this, $columnSep);
	    
	    if (!empty($props))
	    {
	        $conditions .= $this->getWhereClause($props, $table, $listOfSpecialClause, $conditionSep);
	        $conditionSep = ' and ';
	    }
	    
	    if($listOfSpecialClause)
	    {
	    	$special = $this->handleSpecialClauses($listOfSpecialClause);
	    }
	    
        if (empty($conditions))
        {
            $where = '';
        }
        else 
        {
            $where .= $conditions;
        }
		$this->sqlQuery("select $columns from $tables $where $special");
		
		return mysql_num_rows($this->_queryHandle);
	}
	
	private function getAllColumnSql(&$model, $columnSep, $prefixColumnNames = false)
	{
	    $table = $this->escapeTable($model->getTable());
	    $cols = $model->getMemberVariables();
	    
	    $pkey = $model->getPrimaryKey();
	    $ret = $columnSep.$table.'.'.$this->escapeColumn($pkey).' as '.$this->escapeColumn($prefixColumnNames ? $prefixColumnNames.'_'.$pkey : $pkey);
	    foreach ($cols as $col => $value)
	    {
	        if ($col == $pkey)
	        {
	            continue;
	        }
	        $ret .= ', '.$table.'.'.$this->escapeColumn($col).' as '.$this->escapeColumn($prefixColumnNames ? $prefixColumnNames.'_'.$col : $col);
	    }
	    
	    return $ret;
	}
	
	private function getWhereClause($props, $table, $listOfSpecialClause = array(), $conditionSep = '')
	{
	    $ret = '';
        $operators = $this->getOperators($listOfSpecialClause);
        
		foreach ($props as $col => $value)
		{
		    $op = '=';
		    if (isset($operators[$col]))
		    {
		        $op = $operators[$col];
		    }
			$ret .= $conditionSep.$table.'.'.$this->escapeColumn($col).' '.$op.' '.$this->escapeValue($value);
			$conditionSep = ' and ';
		}
		
		return $ret;
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

		return $this->createNewModel($row);
	}
	
	/**
	 * @return object This returns the next model derived from (possibly) multiple rows from the last query.  Check getJoinData(lower case model name) for results. 
	 * @throws SearchException if there was no previous query, or an error occurred
	 */
	public function fetchObject()
	{
	    if (is_null($this->_queryHandle))
	    {
	        throw new SearchException('No query has been made');
	    }
        $column = $this->getPrimaryKey();
        
        if ($this->_fetchObjectBuffer)
        {
    	    $lastPrimaryKey = $this->_fetchObjectBuffer[$column];
    	    $ret = $this->_fetchObjectBuffer;
    	    $this->_fetchObjectBuffer = FALSE;
        }
        else
        {
            $lastPrimaryKey = FALSE;
            $ret = array();
        }
            
        while ($row = mysql_fetch_assoc($this->_queryHandle))
        {
            if ($lastPrimaryKey === FALSE || $row[$column] == $lastPrimaryKey)
            {
                $ret = $this->mergeRows($ret, $row);
                $lastPrimaryKey = $row[$column];
            }
            else
            {
                $this->_fetchObjectBuffer = $row;
                $lastPrimaryKey = $row[$column];
                break;
            }
        }
        
	    return $this->createNewModel($ret);
	}
	
	private function mergeRows($rowA, $rowB)
	{
	    if (!$rowA)
	    {
	        return $rowB;
	    }
	    
	    $ret = $rowA;
	    foreach ($rowB as $key => $value)
	    {
	        $a = array_key_exists($key, $rowA) ? $rowA[$key] : false;
	        $b = $value;

	        if (is_array($a))
	        {
	            $ret[$key][] = $b;
	        }
	        else
	        {
	            // TODO: merging base model's info into an array here
	            $ret[$key] = array($a, $b);
	        }
	    }
	    
	    return $ret;
	}
	
	protected function createNewModel($data)
	{
	    if (empty($data))
	    {
	        return FALSE;
	    }
	    
	    // don't call parent, because we need to inject setJoins after new model has been created, but before populate is called
	    $modelName = get_class($this);
	    $model = new $modelName();
	    
	    $model->setJoins($this->_joinArray);
	    $model->populate($data);
	    
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
		unset($vars['_queryHandle']);
		unset($vars['_query']);

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
	
	private function escapeValue($value)
	{
	    return '\''.mysql_real_escape_string($value).'\'';
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

