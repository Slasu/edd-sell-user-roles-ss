<?php
/*
  Plugin Name: EDD Sell User Roles
  Plugin URI: #
  description: Sławomir Sułkowski - EDD Sell User Roles - Recruitment plugin
  Version: 1.0
  Author: Sławomir Sułkowski
  Author URI: #
  License: GPL2
  */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Check if required plugins are installed & activated. If they are somehow missing, stop further executing.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(!is_plugin_active('easy-digital-downloads/easy-digital-downloads.php')) {
    function notice_missing_edd_plugin(){
            echo '<div class="error">
                    <p>Easy Digital Downloads plugin is missing. It is required for EDD Sell User Roles plugin to work properly.
                    Please install/activate Easy Digital Downloads plugin to use EDD Sell User Roles.</p></div>';
    }
    add_action('admin_notices', 'notice_missing_edd_plugin');
    return;
}

if(!is_plugin_active('edd-auto-register/edd-auto-register.php')) {
    function notice_missing_edd_autoreg_plugin(){
        echo '<div class="error">
                    <p>Easy Digital Downloads - Auto Register (EDD Auto Register) plugin is missing. It is required for 
                    EDD Sell User Roles plugin to work properly. Please install/activate EDD Auto Register plugin to 
                    use EDD Sell User Roles.</p></div>';
    }
    add_action('admin_notices', 'notice_missing_edd_autoreg_plugin');
    return;
}

class EddSellUserRolesSS {

    private static $instance;

    private function setPluginDir()
    {
        if (!defined('EDD_SELL_USER_ROLES_DIR')) {
            define('EDD_SELL_USER_ROLES_DIR', plugin_dir_path(__FILE__));
        }
    }

    private function getRequiredFiles()
    {
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/metabox.php' );
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/set-role.php' );
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/helper.php' );
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/admin/posts-role-column-header.php' );
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/admin/payment-history-regenerate-role.php' );
        require_once( EDD_SELL_USER_ROLES_DIR . 'inc/admin/payment-history-regenerate-role-ajax.php' );
    }

    //Default CPT name for 'product' in EDD plugin
    public static $eddPostType = 'download';

    //key for post meta
    public static $eddSellUserRolesMetaKey = 'EDD_Select_User_Role';

    //default value when no role is selected
    public static $eddSellUserRolesDefaultMetaValue = 'no_role';

    public static function init()
    {
        // Singleton Design Pattern
        if( !isset( self::$instance ) && !( self::$instance instanceof EddSellUserRolesSS) ) {
            self::$instance = new EddSellUserRolesSS();
            self::$instance->setPluginDir();
            self::$instance->getRequiredFiles();
        }

        return self::$instance;
    }

}

EddSellUserRolesSS::init();