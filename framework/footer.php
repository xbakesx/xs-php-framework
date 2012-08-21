    <?php
    try
    {
        $postcontentFile = (isset($controller) && strlen($controller->getFooterFile()) != 0) ? $controller->getFooterFile() : $app->getFooterFile();
        if (strlen($postcontentFile) != 0)
        {
            include_once '../conf/'.$postcontentFile;
        }
        
        $jsFiles = isset($controller) && count($controller->getJs()) > 0 ? $controller->getJs() : $app->getJs();
    	foreach ($jsFiles as $js)
    	{
    	    $url = false;
    	    // urls that start / or http:// are absolute, respect that.
    	    if (substr($js, 0, 1) == '/' || substr($js, 0, 7) == 'http://')
    	    {
    	        $url = $js;
    	    }
    	    else
    	    {
    	        $url = "/js/$js";
    	    }
    	    echo '<script type="text/javascript" src="'.$url.'"></script>'."\n";
    	}
        if (file_exists('js/'.$prefix.'.js'))
        {
            echo "\t".'<script type="text/javascript" src="/js/'.$prefix.'.js"></script>';
        }
    }
    catch (Exception $e)
    {
        echo '<pre>'.$e->getTrace().'</pre>';
    }
    ?>
</body>
</html>
