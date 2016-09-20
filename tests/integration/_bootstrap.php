<?php
// Here you can initialize variables that will be available to your tests

$baseWordPressDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$basePluginDir = dirname(dirname(dirname(__FILE__)));
require_once $baseWordPressDir.'/wp-load.php';
require_once $basePluginDir.'/vendor/autoload.php';
require_once $basePluginDir.'/App/Config/App.php';
require_once $basePluginDir.'/App/Config/RegisterProviders.php';