<h1>All of our students</h1>
<?php 

if ($viewData === false)
{
    debug('You need to specify a student to delete');
}
else
{
    echo '<table class="table">
    <tr><th>Student</th><th>Birthday</th></tr>';
    foreach ($viewData as $student)
    {
        /** @var ClassModel $class */
        echo "<tr><td>{$student->getName()}</td><td>{$student->getBirthday()}</td></tr>";
    }
    echo '</table>';
}
?>
