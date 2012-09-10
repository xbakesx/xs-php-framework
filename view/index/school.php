<h1>Welcome to XS - <em>The</em> PHP Framework</h1>

<?php

debug($viewData);
    
if (is_array($viewData))
{
    echo '<table class="table">
    <tr><th>Student</th><th>Class</th></tr>';
    foreach ($viewData as $student)
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
