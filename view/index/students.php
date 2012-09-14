<h1>All of our students</h1>
<?php 
if ($viewData['limit'])
{
?>
<h2>Students born after <?php echo $viewData['limit']; ?></h2>
<?php
} 
?>
<?php

echo '<table class="table">
<tr><th>Student</th><th>Birthday</th></tr>';
foreach ($viewData['students'] as $student)
{
    /** @var ClassModel $class */
    echo "<tr><td>{$student->getName()}</td><td>{$student->getBirthday()}</td></tr>";
}
echo '</table>';

?>