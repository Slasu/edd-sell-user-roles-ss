<?php
/*
 * Ajax function for regenerating user roles
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function regenerateUserRole() {
    if( !isset($_POST['data']['userId']) || empty($_POST['data']['userId'])
        || !isset($_POST['data']['role']) || empty($_POST['data']['role']) ) die();

    global $wp_roles;
    $user_id = $_POST['data']['userId'];
    $role = $_POST['data']['role'];

    $response = [];

    $user = new WP_User($user_id);
    if( !in_array($role, $user->roles) ) {
        $user->add_role($role);
        if( in_array($role, $user->roles) ) {
            $role_name = translate_user_role( $wp_roles->roles[ $role ]['name'] );
            $response['message'] = sprintf( __('Role %s added to the user.', 'ESUR'), $role_name );
            echo json_encode($response);
            die();
        }

        //This may happen when role that does not exist in the system anymore has been purchased (role has been deleted/renamed)
        $response['message'] = __('Something went wrong and this is probably unrelated to EDD Sell User Roles plugin. 
        Please check your roles and EDD products configuration.', 'ESUR');
        echo json_encode($response);
        die();
    }

    $response['message'] = __('User already has that role', 'ESUR');
    echo json_encode($response);
    die();
}

//We need ajax only on the admin panel, so no use in adding wp_ajax_nopriv_
add_action('wp_ajax_regenerateUserRole', 'regenerateUserRole');