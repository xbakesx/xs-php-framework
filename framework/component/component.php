<?php
abstract class Component 
{
	public static $components;
	
	public static function get($componentName, $args = NULL)
	{
	    if (!isset(Component::$components))
	    {
	        Component::$components = new MyComponents();
	    }
	    if (is_null($args))
	    {
	        $componentData = Component::$components->$componentName();
	    }
	    else
	    {
	        $componentData = Component::$components->$componentName($args);
	    }
	    include '../component/'.$componentName.'.php';
	}
	
	public function includeModel($models)
	{
	    if (!is_array($models))
	    {
	        $models = array($models);
	    }
	    
        includeElement($models, 'model');
	}
}
?>