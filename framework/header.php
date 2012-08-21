<?php
    $doctype = (isset($controller) && strlen($controller->getDoctype()) != 0) ? $controller->getDoctype() : $app->getDoctype();
    echo $doctype; 
    $isHtml5 = $doctype == AppInterface::HTML_VERSION_5;
    unset($doctype);
?>
<html>
    <head>
        <?php
            $charset = (isset($controller) && strlen($controller->getCharSet())) != 0 ? $controller->getCharSet() : $app->getCharSet();
            if ($isHtml5)
            {
                echo '<meta charset="'.$charset.'">';
            } 
            else
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'">';
            }
            unset($charset);
        ?>
        <title><?php echo $app->getTitle().(isset($controller) ? $controller->getTitle() : ''); ?></title>
    	<meta name="author" content="<?php echo $app->getAuthor().(isset($controller) ? $controller->getAuthor() : ''); ?>">
    	<meta name="Keywords" content="<?php
    	    $appKeywords = $app->getKeywords();
    	    if (is_array($appKeywords))
    	    {
    	        $appKeywords = implode(',', $appKeywords);
    	    }
    	    $controllerKeywords = isset($controller) ? $controller->getKeywords() : array();
    	    if (is_array($controllerKeywords))
    	    {
    	        $controllerKeywords = implode(',', $controllerKeywords);
    	    }
    	     
    	    echo $appKeywords.(strlen($controllerKeywords) == 0 ? '' : ','.$controllerKeywords); 
    	?>">
    	<meta name="description" content="<?php echo $app->getTitle().(isset($controller) ? $controller->getTitle() : ''); ?>">
    
        <?php 
            if ($isHtml5) 
            {
                echo '<!-- HTML5 shim, for IE6-8 support of HTML elements -->
            <!--[if lt IE 9]>
              <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->'."\n";
            }
            
            $favicon = $app->getFaviconUrl();
        	if (!empty($favicon))
        	{
        	    echo '<link rel="shortcut icon" href="'.$favicon.'">'."\n";
        	}
        	
        	$cssFiles = isset($controller) && $controller->getCss() !== FALSE ? $controller->getCss() : $app->getCss();
        	foreach ($cssFiles as $css)
        	{
        	    $url = false;
        	    // urls that start / or http:// are absolute, respect that.
        	    if (substr($css, 0, 1) == '/' || substr($css, 0, 7) == 'http://')
        	    {
        	        $url = $css;
        	    }
        	    else
        	    {
        	        $url = "/css/$css";
        	    }
        	    echo '<link rel="stylesheet" type="text/css" href="'.$url.'">'."\n";
        	}
        	
        	if (file_exists('css/'.$prefix.'.css'))
        	{
        	    echo '<link rel="stylesheet" type="text/css" href="/css/'.$prefix.'.css">';
        	}
 	    ?>
    </head>
  <body>
  <?php
        $precontentFile = isset($controller) && strlen($controller->getHeaderFile()) != 0 ? $controller->getHeaderFile() : $app->getHeaderFile();
        if (strlen($precontentFile) != 0)
        {
            include_once '../conf/'.$precontentFile;
        }
  ?>
    