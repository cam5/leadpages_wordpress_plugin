<?php

/*
Plugin Name: Leadpages Connector
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description:
Author: Leadpages
Version: 2.1.0
Author URI: http://leadpages.net
*/
$plugin_version = '2.1.0';
define('REQUIRED_PHP_VERSION', 5.4);

checkPHPVersion($plugin_version);


use LeadpagesWP\Lib\Update;



  /*
    |--------------------------------------------------------------------------
    | Application Entry Point
    |--------------------------------------------------------------------------
    |
    | This will be your plugin entry point. This file should
    | not contain any logic for your plugin what so ever.
    |
    */



require_once('c3.php');
require_once('vendor/autoload.php');
require_once('App/Config/App.php');
require_once($leadpagesConfig['basePath'] . 'Framework/ServiceContainer/ServiceContainer.php');
require_once($leadpagesConfig['basePath'].'App/Config/RegisterProviders.php');


/*
  |--------------------------------------------------------------------------
  | Fix Database items from plugin version 2.0 and 2.0.1
  |--------------------------------------------------------------------------
  */
require_once($leadpagesConfig['basePath'].'App/Lib/RevertChanges.php');
/*
  |--------------------------------------------------------------------------
  | Register Auto Update
  |--------------------------------------------------------------------------
  */
require_once($leadpagesConfig['basePath'].'App/Lib/Update.php');
$update = new Update();
$update->register_auto_update();
$update->scheduleCacheUpdates();

/*
  |--------------------------------------------------------------------------
  | Admin Bootstrap
  |--------------------------------------------------------------------------
  |
  */


if (is_admin() || is_network_admin()) {
    $adminBootstrap = $leadpagesApp['adminBootstrap'];
    //include('App/Helpers/ErrorHandlerAjax.php');
}

function getScreen()
{
    global $leadpagesConfig;

    $screen = get_current_screen();
    $leadpagesConfig['currentScreen'] = $screen->post_type;
    $leadpagesConfig['currentScreenAll'] = $screen;
}

add_action('current_screen', 'getScreen');


/*
  |--------------------------------------------------------------------------
  | Front Bootstrap
  |--------------------------------------------------------------------------
  |
  |
  |
  */

if (!is_admin() && !is_network_admin()) {
    $frontBootstrap = $leadpagesApp['frontBootstrap'];
    //include('App/Helpers/ErrorHandlerAjax.php');
}





//Check PHP VERSION BEFORE ANYTHING
function checkPHPVersion($plugin_version)
{
    if ( version_compare( PHP_VERSION, REQUIRED_PHP_VERSION, '<' ) ){
        $activePlugins = get_option('active_plugins', true);
        foreach($activePlugins as $key => $plugin){
            if($plugin == 'leadpages/leadpages.php'){
                unset($activePlugins[$key]);
            }
        }
        update_option('active_plugins', $activePlugins);
        $current_php_version = PHP_VERSION;
        wp_die('<p>The <strong>Leadpages&reg;</strong> plugin version '.$plugin_version.' requires php version <strong> '.REQUIRED_PHP_VERSION.' </strong> or greater.</p>
    <p>You are currently using <strong>'.PHP_VERSION.'</strong></p>
    <p>Please use plugin version 1.2', 'Plugin Activation Error',  array('back_link'=>TRUE ) );
    }
}


