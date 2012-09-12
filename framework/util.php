<?php

/**
 * This file is filled with php functions that every page will be loading.  
 * Careful what you put in here.
 */

function debug($var)
{
	echo '<pre>';
    if (is_object($var))
    {
        var_dump($var);
    }
    else
    {
        print_r($var);
    }
    echo '</pre>';
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

/**
 * @param string $str string to capitalize
 * @return returns a capitalized version of the string passed in (uppercase the first letter, leaves the rest alone)
 */
function strtocap($str)
{
    return strtoupper(substr($str, 0, 1)).substr($str, 1);
}