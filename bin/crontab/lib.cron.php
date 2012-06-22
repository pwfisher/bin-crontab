<?php

chdir(dirname(__FILE__));
chdir("../../");
//echo getcwd() . "\n";

include_once 'fct/lib.global.php';
include_once 'fct/class.db.php';

set_time_limit(0);

// Get Passed Vars
if (isset($argv) && $argv) {
	// get vars in unix
	$args = parseArgs($argv);

	foreach ($args as $arg) {
		$arg = explode("=", $arg);
		${$arg[0]} = $arg[1];
	}
} else {
	$args = $_REQUEST;
	foreach ($args as $key => $value) {
		${$key} = $value;
	}
}

//http://pwfisher.com/nucleus/index.php?itemid=45
function parseArgs($argv)
{
    array_shift($argv);
    $out = array();
    foreach ($argv as $arg) {
        if (substr($arg, 0, 2) == '--') {
            $eqPos = strpos($arg, '=');
            if ($eqPos === false) {
                $key = substr($arg, 2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            } else {
                $key = substr($arg, 2, $eqPos-2);
                $out[$key] = substr($arg, $eqPos+1);
            }
        } else if (substr($arg, 0, 1) == '-') {
            if (substr($arg, 2, 1) == '=') {
                $key = substr($arg, 1, 1);
                $out[$key] = substr($arg, 3);
            } else {
                $chars = str_split(substr($arg, 1));
                foreach ($chars as $char) {
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
        } else {
            $out[] = $arg;
        }
    }
    return $out;
}
?>