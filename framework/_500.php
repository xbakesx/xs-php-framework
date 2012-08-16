<div class="centered">
    <h1 class="http_error">500</h1>
    <p class="http_error_message">We are experiencing some technical issues.  Fear not, our crack team of engineers have already been informed and are currently working on the problem.</p>
</div>

<?php
    if ($app->isDebug())
    {
        echo '<div>';
        global $exception;
        
        if (error_get_last() !== null)
        {
            $error = error_get_last();
            echo '<h2>'.$error['message'].'</h2>';
            echo '<pre>In '.$error['file']." on line ".$error['line'].'</pre>';
        }
        
        if ($exception)
        {
            echo '<h2>'.$exception->getMessage().'</h2>';
            $traces = $exception->getTrace();
            $info = array_shift($traces);
            echo '<pre>Thrown from '.$info['args'][2]." on line ".$info['args'][3].'</pre>';
            foreach ($traces as $trace)
            {
                echo '<pre>In '.$trace['function'].'() in '.$trace['file'].' on line '.$trace['line'].'</pre>';
            }
        }
        echo '</div>';
    }
?>