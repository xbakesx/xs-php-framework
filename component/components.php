<?php

class MyComponents extends Component
{
    /**
     * These methods get run before your html is included.
     * 
     * In your view:
     *  - Component::get('sample', $args)
     * In component/components.php (that's this file)
     *  - sample($args)
     * In component/sample.php
     *  - write some html and php $componentData has the return of sample($args)
     *  
     * $this->includeModel('sampleModel.php') or $this->includeModel(array('sampleModel.php','otherModel.php')) to include models
     */
    
     public function sample($args)
     {
         return $args;
     }
}

?>