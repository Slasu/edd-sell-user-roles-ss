<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function eddSellUserRolesMetabox() {
    $edd_post_type = apply_filters( 'edd_download_metabox_post_types' , array( EddSellUserRolesSS::$eddPostType ) );

    add_meta_box('edd_sell_user_roles', sprintf(__('%1$s Roles', 'easy-digital-downloads'), edd_get_label_singular(), edd_get_label_plural()), 'edd_render_sell_user_roles_metabox', $edd_post_type, 'normal', 'high');
}
add_action('add_meta_boxes', 'eddSellUserRolesMetabox');

function edd_render_sell_user_roles_metabox() {
    global $post;
    $selected_role = get_post_meta($post->ID, EddSellUserRolesSS::$eddSellUserRolesMetaKey, true);
    ?>
    <p>
        <?php
            echo __('Select user role that you want to sell with this product. Remember, user role can be sold only with a Default
            product type (that means separate Default product, or as a part of a Bundle). Assigning product role to a Bundle
            product will not work.', 'ESUR');
        ?>
    </p>
    <select id="EDD_Select_User_Role" name="EDD_Select_User_Role">
        <option value="<?php echo EddSellUserRolesSS::$eddSellUserRolesDefaultMetaValue;?>"
            <?php if(!isset($selected_role) || empty($selected_role) || $selected_role== EddSellUserRolesSS::$eddSellUserRolesDefaultMetaValue) echo 'selected';?>>
            <?php echo __('No role', 'ESUR');?>
        </option>
        <?php wp_dropdown_roles($selected_role);?>
    </select>

<?php }

//Action for saving specific post type 'download' rather than all posts
add_action( 'save_post_'.EddSellUserRolesSS::$eddPostType, 'save_edd_user_role', 10, 2 );

function save_edd_user_role($post_id, $post){
    //Make sure the post type is download, even though using 'save_post_download' action
    $edd_post_type = EddSellUserRolesSS::$eddPostType;
    if($post->post_type != $edd_post_type) return;

    $selected_role = get_post_meta($post->ID, EddSellUserRolesSS::$eddSellUserRolesMetaKey, true);

    if(isset($_POST['EDD_Select_User_Role'])){

        $ESUR_post_meta_key = EddSellUserRolesSS::$eddSellUserRolesMetaKey;
        $ESUR_default_value = EddSellUserRolesSS::$eddSellUserRolesDefaultMetaValue;

        /*
         * From what I understand, roles can be bought only with a 'Default' product, not the 'bundle' one (or from a default
         * product that is in the bundle). That is why if someone tries to assign any role to a 'bundle' product type,
         * I have to make sure it won't get assigned. If 'bundle' product type is selected, the role will be automatically
         * changed to 'no_role' - they key for the default role value that means no role has been selected.
         */
        if($_POST['_edd_product_type'] == 'bundle') {
            if(!in_array(EddSellUserRolesSS::$eddSellUserRolesMetaKey, get_post_custom_keys($post->ID))) {
                add_post_meta($post->ID, $ESUR_post_meta_key, $ESUR_default_value);
            } elseif($selected_role != $ESUR_default_value){
                update_post_meta($post->ID, $ESUR_post_meta_key, $ESUR_default_value);
            }
            return;
        }

        if(!in_array($ESUR_post_meta_key, get_post_custom_keys($post->ID))) {
            add_post_meta($post->ID, $ESUR_post_meta_key, $_POST[$ESUR_post_meta_key]);
        } elseif($selected_role != $_POST[$ESUR_post_meta_key]){
            update_post_meta($post->ID, $ESUR_post_meta_key, $_POST[$ESUR_post_meta_key]);
        }
    }
}
?>