<div class="centered">
    <h1 class="http_error">404</h1>
    <?php
    if ($app->isDebug())
    {
        echo '<p class="http_error_message">You need to create a php file at <code>'.$viewFile.'</code> in order for a page to show up here.</p>';
    } 
    else
    {
        echo '<p class="http_error_message">The page you are looking for could not be found.</p>';
    }
    ?>
</div>
