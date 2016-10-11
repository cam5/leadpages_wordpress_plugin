<?php


namespace LeadpagesWP\Admin\SettingsPages;

use TheLoop\Contracts\SettingsPage;

class LeadpagesLoginPage implements SettingsPage
{
    public static function getName(){
        return get_called_class();
    }

    public function definePage() {
        global $leadpagesConfig;
        add_menu_page('leadpages', 'Leadpages', 'manage_options', 'Leadpages', array($this, 'displayCallback'), 'none' );
    }


    public function displayCallback(){
        if(isset($_GET['code'])){
            $code = sanitize_text_field($_GET['code']);
            echo '<div class="notice notice-error is-dismissible"><p>Login Failed Error Code: '. esc_html($code) .'</p></div>';
        }

        $html = file_get_contents('https://leadbrite.appspot.com/plugin_login_page');
        echo $html;
    }

    public function registerPage(){
        add_action( 'admin_menu', array($this, 'definePage') );
    }

}