<?php

session_start();

require_once '../framework/controller/controller.php';
require_once '../framework/component/component.php';
require_once '../framework/app.php';
require_once '../framework/model/model.php';
require_once '../framework/db.php';
require_once '../framework/util.php';
require_once '../conf/app.php';
require_once '../component/components.php';

//Defaults
$controllerSuffix = 'Controller';
$modelSuffix = 'Model';
$defaultName = 'index';

// configure app
$app = new App();
App::$DATABASE_CONNECTIONS = $app->getDatabaseConnections();

// parse url
$path = explode('/', $_SERVER['REQUEST_URI']);

// remove the $_GET parameters from the last argument
$last_arg = array_pop($path);
$getstr = divide($last_arg, '?');
unset($last_arg);
array_push($path, array_shift($getstr));

array_shift($path); // remove empty first element because paths start with /

$prefix = array_shift($path);
if ($prefix === null || strlen($prefix) == 0)
{
    $prefix = $defaultName;
}
$prefix = divide($prefix, '.', true);
$lowerPrefix = $prefix;
$prefix = strtoupper(substr($prefix, 0, 1)).substr($prefix, 1);

$controllerName = $prefix.$controllerSuffix;
$controllerMethod = array_shift($path);
if ($controllerMethod === null || strlen($controllerMethod) == 0)
{
    $controllerMethod = $defaultName;
}
$controllerMethod = divide($controllerMethod, '.', true);
$controllerArgs = $path;

$controllerFile = '../controller/'.$controllerName.'.php';
$modelFile = '../model/'.$prefix.$modelSuffix.'.php';
$viewFile = '../view/'.$lowerPrefix.'/'.$controllerMethod.'.php';
$viewData = array();

$viewException = NULL;

if (file_exists($controllerFile))
{
    $controller = includeController($controllerName, $controllerFile, $app);

    if ($controller->isAuthorized())
    {
        try
        {
            if (method_exists($controller, $controllerMethod))
            {
                $viewData = $controller->$controllerMethod($controllerArgs);
            }
            else if (method_exists($controller, 'index'))
            {
                // add the method back in as a controller argument
                array_unshift($controllerArgs, $controllerMethod);
                $viewData = $controller->index($controllerArgs);
                $viewFile = '../view/'.$lowerPrefix.'/index.php';
            }
            else
            {
                if ($app->isDebug())
                {
                    echo '<pre>'.get_class($controller).' has no method '.$controllerMethod.'</pre>';
                }
            }
        }
        catch (Exception $ex)
        {
            $viewException = $ex;
        }
    }
}
else if (file_exists($modelFile))
{
    // the controller didn't include this, so we should
    require_once $modelFile;
}

require_once('../framework/header.php');

if ($app->isDebug() && $viewException)
{
    debug($viewException);
}

if (file_exists($viewFile))
{
    if (!isset($controller) || $controller->isAuthorized())
    {
        try
        {
            include_once $viewFile;

            if ($app->isDebug() && error_get_last() !== null)
            {
                require_once '../framework/_500.php';
            }


        }
        catch (Exception $exception)
        {
            include_once '../framework/_500.php';
        }
    }
    else
    {
        if (file_exists('../model/UserModel.php'))
        {
            include_once('../model/UserModel.php');
        }
         
        //@TODO: magic currently happening with the login page.
        if (file_exists('../view/user/login.php'))
        {
            include_once '../controller/UserController.php';
            $controller = new UserController($app);
            if (method_exists($controller, 'login'))
            {
                $viewData = $controller->login($controllerArgs);
            }
             
            include_once '../view/user/login.php';
             
            if ($app->isDebug() && error_get_last() !== null)
            {
                require_once '../framework/_500.php';
            }
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

require_once('../framework/footer.php');


function includeElement($items, $type)
{
    foreach($items as $item)
    {
        $itemLocation = '../'.$type.'/'.$item;
        $objName = divide($item,'.',true);
        if(file_exists($itemLocation) && !class_exists($objName))
        {
            require_once $itemLocation;
        }
    }
}

function includeController($controllerName, $controllerFile, $app)
{
    require_once $controllerFile;
    /* @var $controller Controller */
    $controller = new $controllerName($app);
    
    // controller's model
    global $modelFile;
    includeElement(array($modelFile), 'model');
    // get external models
    includeElement($controller->getModels(), 'model');
    
    return $controller;
}

