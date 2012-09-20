<h1>Model / View / Controller</h1>
<p>Below you will see a table populated from data procured from a model (that work was done in the controller).  Then formatted into HTML in a view.</p>
<?php


if (is_array($viewData))
{
    echo '<table class="table">
    <tr><th>Class</th><th>Teacher</th><th>Students</th></tr>';
    foreach ($viewData as $classModel)
    {
        $className = $classModel->getName();
        
        $teachers = $classModel->getJoinData('teachermodel');
        $teacherModel = array_shift($teachers);
        $teacherName = $teacherModel->getName();
        
        
        $students = $classModel->getJoinData('studentmodel');
        
        if (is_array($students))
        {
            $rows = count($students);
            $firstStudent = array_shift($students);
            
            echo "<tr><td rowspan=\"$rows\">$className</td><td rowspan=\"$rows\">$teacherName</td><td>{$firstStudent->getName()} ({$firstStudent->getBirthday()})</td></tr>";
            
            foreach ($students as $studentModel)
            {
                echo "<tr><td>{$studentModel->getName()} ({$studentModel->getBirthday()})</td></tr>";
            }
        }
        else
        {
            echo "<tr><td rowspan=\"$rows\">$className</td><td rowspan=\"$rows\">$teacherName</td><td></td></tr>";
        }
    }
    echo '</table>';
}
else
{
    echo $viewData;
}

?>
