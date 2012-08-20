<h1>Welcome to XS - <em>The</em> PHP Framework</h1>

<?php
    
if (is_array($viewData))
{
    echo '<table class="table">
    <tr><th>Class</th><th>Teacher</th></tr>';
    foreach ($viewData as $class)
    {
        /** @var ClassModel $class */
        echo "<tr><td>{$class->getName()}</td><td>{$class->getTeacher()->getName()}</td></tr>"; 
    }
    echo '</table>';
}
else
{
    echo $viewData;
}

?>
