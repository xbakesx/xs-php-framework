<?php

interface PersistentStore
{
    public function create();
    public function createOrUpdate();
    public function update();
    public function delete();
    
    public function search();
}

abstract class DatabaseModel extends PersistentStore
{
    public function create()
    {
        // takes all the member variables and does a database insert
    }
    
    public function createOrUpdate()
    {
        // takes all the member variables, checks for existence, if it exists does an update, otherwise a create
    }
    
    public function update()
    {
        // takes all the member variables and does an database update
    }
    
    public function delete()
    {
        // takes the member variables and deletes matching rows in database
    }
    
    public function search()
    {
        // takes the set member variables and returns an array of of matching rows
    }
    
    /**
     * Should probably have other methods for better searching, like search(), that does the query then return something we can iterate over that will actually do the fetching one row at a time...
     */
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