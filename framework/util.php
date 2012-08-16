<?php

/**
 * This file is filled with php functions that every page will be loading.  
 * Careful what you put in here.
 */
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