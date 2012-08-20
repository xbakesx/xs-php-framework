<h1>Welcome to XS - <em>The</em> PHP Framework</h1>

<table class="table">
    <tr><th>Class</th><th>Teacher</th></tr>
<?php
    
    foreach ($viewData as $class)
    {
        /** @var ClassModel $class */
        echo "<tr><td>{$class->getName()}</td><td>{$class->getTeacher()->getName()}</td></tr>"; 
    }
?>
</table>
