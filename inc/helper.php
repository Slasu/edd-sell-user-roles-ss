<?php
/*
 * Helper class to keep helper functions used in EDD Sell User Roles in one place
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ESURHelper {

    public static function setUserRole($role, $user) {
        global $wp_roles;
        $role_name = translate_user_role( $wp_roles->roles[ $role ]['name'] );

        /*
         * I am adding a new role and not removing the old one, because if someone pays for a new role, he/she may want some
         * addition to current privileges, without removing them. WordPress supports few user roles for one user.
         */
        $user->add_role($role);
        return sprintf( __('You have bought %s role with this product.', 'ESUR'), $role_name );
    }

    public static function regenerateRoleDisplayInfo($role, $user, $user_id) {
        global $wp_roles;
        $role_name = translate_user_role( $wp_roles->roles[ $role ]['name'] );

        //This may happen when role that does not exist in the system anymore has been purchased (role has been deleted/renamed)
        if(!isset($role_name) || empty($role_name)) $role_name = 'Unknown role';
        echo '<div class="regenerateRoleSingleHolder" style="margin: 10px 0;">';
        echo '<span style="display: inline-block; padding: 4px 5px 4px 0;"><strong>' . $role_name . '</strong>:</span>';
        echo !in_array($role, $user->roles) ?
            '<div class="RegRoleButton button button-secondary" onclick=\'addRole(this,"' . $role . '",' . $user_id .')\'>'. __("Regenerate user role", "ESUR") . '</div>
                <div class="RegenerateRoleResponse" style="display: inline;"></div>' :
            __('User already has that role', 'ESUR');
        echo '</div>';
    }
}