<h1>Welcome to XS - <em>The</em> PHP Framework</h1>

<pre>
    It appears that the join does not populate joined model correctly.
    
    Also, make sure to try doing a many-to-many join, with another join to get the class's teacher information.
</pre>

<?php
if (is_array($viewData))
{
    echo '<table class="table">
    <tr><th>Student</th><th>Class</th></tr>';
    foreach ($viewData as $student)
    {
        $classes = $student->getClasses();
        
        if (is_array($classes) > 0)
        {
            $count = count($classes);
            
            break;
            echo "<tr><td rowspan=\"$count\">{$student->getName()}</td>";
            
            $firstClass = array_shift($classes);
            echo "<td>{$class->getName()}</td><tr>";
            
            foreach ($classes as $class)
            {
                echo "<tr><td>{$class->getName()}</td><tr>";
            }
        }
        else if (is_object($classes))
        {
            echo "<tr><td>{$student->getName()}</td><td>{$classes->getName()}</td></tr>";
        }
        else
        {
            echo "<tr><td>{$student->getName()}</td><td></td></tr>";
        }
    }
    echo '</table>';
}
else
{
    echo $viewData;
}

?>
