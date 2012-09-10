<h1>Model / View / Controller</h1>
<p>Below you will see a table populated from data procured from a model (that work was done in the controller).  Then formatted into HTML in a view.</p>
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
