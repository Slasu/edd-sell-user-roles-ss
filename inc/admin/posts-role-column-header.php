<?php

if ( ! defined( 'ABSPATH' ) ) exit;

//Displays role name in All Downloads WordPress backend page (/wp-admin/edit.php?post_type=download).
function add_role_name_column($defaults) {
    $defaults['role_name'] = 'Role';
    return $defaults;
}
function display_download_name_column($column_name, $post_ID) {
    if ($column_name == 'role_name') {
        global $wp_roles;
        $role = get_post_meta($post_ID, EddSellUserRolesSS::$eddSellUserRolesMetaKey, true);
        $role_name = translate_user_role( $wp_roles->roles[ $role ]['name'] );
        if ($role_name) {
            echo $role_name;
        }
    }
}
//'edd_download_columns' filter could also be used, but I find this one more reliable
add_filter('manage_edit-download_columns', 'add_role_name_column');
add_action('manage_download_posts_custom_column', 'display_download_name_column', 10, 2);