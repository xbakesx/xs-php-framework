<h1>This is a sample Component</h1>
<?php
    
    if (is_array($componentData) || is_object($componentData))
    {
    
?>
    <p>We can use it anywhere to display an unordered list</p>
    <ul>
    <?php 
        foreach($componentData as $item)
        {
            echo '<li>'.$item.'</li>';
        }
    ?>
    </ul>
<?php
    }
    else
    {
?>
    <p>We can use it to display a string '<?php echo $componentData; ?>'</p>
<?php
    }
?>
