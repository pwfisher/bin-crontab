<?
include('lib.cron.php');
include('fct/class.curl.php');
include('fct/lib.parse.php');
//include(ROOT.'/fct/class.proxy.php');

$proxy->cleanProxies();
$proxy->parseProxy();

$proxy->testProxies("status IS NULL", 1000); //new
$proxy->testProxies("status = 0", 100); //bad
//$proxy->testProxies("status = 1", 100); //good - tested when used
?>