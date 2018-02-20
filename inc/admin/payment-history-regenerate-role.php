<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function add_regenerate_role_metabox($payment_id) { ?>
    <div id="edd-regenerate-user-roles" class="postbox">
        <h3 class="hndle"><?php echo __('Regenerate user roles', 'ESUR'); ?></h3>
        <p class="inside"><?php echo __('If something went wrong, here you can add purchased role to the user manually. If the user doesn\'t 
        have purchased role assigned to his account, button for adding that role will be enabled.', 'ESUR');?></p>
        <div class="inside">
            <?php
            $payment_meta = get_post_meta($payment_id, '_edd_payment_meta');
            $user_id = $payment_meta[0]['user_info']['id'];
            $user = new WP_User($user_id);

            if (edd_is_payment_complete($payment_id)) {
                $purchased_products = $payment_meta[0]['downloads'];

                foreach ($purchased_products as $product) {

                    /*
                     * In case of product bundle, we need to check each product if it contains any role. If it does, enable
                     * regeneration for that role to the user.
                     */
                    if (edd_is_bundled_product($product['id'])) {
                        $bundled_products = edd_get_bundled_products($product['id']);

                        foreach ($bundled_products as $bundle_item) {
                            $role = get_post_meta($bundle_item, 'EDD_Select_User_Role', true);

                            if (isset($role) && !empty($role) && $role != 'no_role') {
                                ESURHelper::regenerateRoleDisplayInfo($role, $user, $user_id);
                            }
                        }

                        continue;
                    }

                    $role = get_post_meta($product['id'], 'EDD_Select_User_Role', true);

                    if (isset($role) && !empty($role) && $role != 'no_role') {
                        ESURHelper::regenerateRoleDisplayInfo($role, $user, $user_id);
                    }
                }
            } ?>
        </div>
    </div>

    <script>
        jQuery('.RegRoleButton').click(function(e){
            e.preventDefault();
        });
        function addRole(elem, role, userId) {
            var responseBox = jQuery(elem).next('.RegenerateRoleResponse');
            jQuery(this).css('display', 'none');
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'regenerateUserRole',
                    data: {role: role, userId: userId},
                }, success: function(result) {
                    var res = JSON.parse(result);
                    jQuery(elem).css('display', 'none');
                    responseBox.html(res['message']);
                }
            });
        }
    </script>

<?php
}
add_action( 'edd_view_order_details_billing_before', 'add_regenerate_role_metabox', 9999, 1 );