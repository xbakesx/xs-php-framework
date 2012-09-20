    <?php
    try
    {
        $appHeader = $app->getFooterFile();
        $postcontentFile = isset($controller) && $controller->getFooterFile() !== $appHeader ? $controller->getFooterFile() : $appHeader;
        if ($postcontentFile)
        {
            include_once '../conf/'.$postcontentFile;
        }

        $jsFiles = isset($controller) ? $controller->getAllJs() : $app->getJs();
        
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
        if (file_exists('js/'.$lowerPrefix.'.js'))
        {
            echo "\t".'<script type="text/javascript" src="/js/'.$lowerPrefix.'.js"></script>';
        }
    }
    catch (Exception $e)
    {
        echo '<pre>'.$e->getTrace().'</pre>';
    }
    ?>
</body>
</html>
