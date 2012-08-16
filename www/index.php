<?php

session_start();
register_shutdown_function('shutdown');

function shutdown()
{
    if (error_get_last() !== null)
    {
        global $app;
        include_once('../framework/header.php');
        include_once('../framework/_500.php');
        include_once('../framework/footer.php');
    }
}

require_once '../framework/controller/controller.php';
require_once '../framework/db.php';
require_once '../framework/util.php';
require_once '../conf/app.php';

// configure app
$app = new App();

// parse url
$path = explode('/', $_SERVER['REQUEST_URI']);

// handle the fact that $_GET is not populated
$last_arg = array_pop($path);
$getstr = divide($last_arg, '?');
unset($last_arg);
array_push($path, array_shift($getstr));
$getstr = array_pop($getstr);
$getarray = explode('&', $getstr);
$get = array();
foreach ($getarray as $getpair)
{
    $pair = divide($getpair, '=');
    $get[$pair[0]] = $pair[1];
}
unset($getstr);
unset($getarray);

array_shift($path); // remove empty first element because paths start with /

$prefix = array_shift($path);
if ($prefix === null || strlen($prefix) == 0)
{
    $prefix = 'index';
}
$prefix = divide($prefix, '.', true);

$controllerName = $prefix.'Controller';
$controllerMethod = array_shift($path);
if ($controllerMethod === null || strlen($controllerMethod) == 0)
{
    $controllerMethod = 'index';
}
$controllerMethod = divide($controllerMethod, '.', true);
$controllerArgs = $path;

$controllerFile = '../controller/'.$controllerName.'.php';
$modelFile = '../model/'.$prefix.'.php';
$viewFile = '../view/'.$prefix.'/'.$controllerMethod.'.php';
$viewData = array();
ob_start();

if (file_exists($controllerFile))
{
    include_once $controllerFile;
    $controller = new $controllerName;

    if ($controller->isAuthorized())
    {
        if (method_exists($controller, $controllerMethod))
        {
            $viewData = $controller->$controllerMethod($controllerArgs, $get);
        }
        else
        {
            if ($app->isDebug())
            {
                echo '<pre>'.get_class($controller).' has no method '.$controllerMethod.'</pre>';
            }
        }
    }
}

if (file_exists($viewFile))
{
    if (!isset($controller) || $controller->isAuthorized())
    {
        try
        {
            include_once $viewFile;
        }
        catch (Exception $exception)
        {
            include_once '../framework/_500.php';
        }
    }
    else
    {
        if (file_exists('../view/user/login.php'))
        {
            include_once '../controller/UserController.php';
            $controller = new UserController();
            if (method_exists($controller, 'login'))
            {
                $viewData = $controller->login($controllerArgs, $get);
            }
            include_once '../view/user/login.php';
        }
        else
        {
            include_once '../framework/_403.php';
        }
    }
}
else
{
    include_once '../framework/_404.php';
}

$contents = ob_get_contents();
ob_end_clean();

try
{
    require_once('../framework/header.php');
    echo $contents;
    if ($app->isDebug() && error_get_last() !== null)
    {
        require_once '../framework/_500.php';
    }
    require_once('../framework/footer.php');
}
catch (Exception $ex)
{
    echo '<pre>'.$ex->getTrace().'</pre>';
}

function divide($str, $sep, $returnFirst = false)
{
    $index = strpos($str, $sep);
    
    if ($index !== false)
    {
        $ret = array(substr($str, 0, $index), substr($str, $index + strlen($sep)));
    }
    else
    {
        $ret = array($str, '');
    }
    
    if ($returnFirst === true)
    {
        return array_shift($ret);
    }
    else
    {
        return $ret;
    }
}

?>
