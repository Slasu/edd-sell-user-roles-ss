<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function edd_set_user_role($item_id, $payment_id) {
    $user_id = get_current_user_id();
    $user = new WP_User( $user_id );

    //Make sure payment is done
    if(edd_is_payment_complete( $payment_id )) {

        /*
         * In case of product bundle, we need to check each product in that bundle if it contains any role. If it does,
         * set that role to the user.
         */
        if(edd_is_bundled_product($item_id)) {
            $bundled_products = edd_get_bundled_products( $item_id );

            foreach ( $bundled_products as $bundle_item ) {
                $role = get_post_meta($bundle_item, 'EDD_Select_User_Role', true);

                if (isset($role) && !empty($role) && $role != 'no_role') {
                    $msg = ESURHelper::setUserRole($role, $user);
                    echo $msg;
                }
            }

            return;
        }

        $role = get_post_meta($item_id, 'EDD_Select_User_Role', true);

        if (isset($role) && !empty($role) && $role != 'no_role') {
            $msg = ESURHelper::setUserRole($role, $user);
            echo $msg;
        }
    }
}

/*
 * I've chosen 'edd_purchase_receipt_after_files' hook because of a few reasons:
 * - it allows to add info with role that has been purchased right under the product(s)
 * - it fires even when no downloadable files are added to the product (that excludes 'edd_receipt_files' hook)
 * - it gives information about the CURRENT item in loop and payment, so there is no need to extracting products from
 * payment, which is pretty easy though (this can be found in inc/admin/payment-history-regenerate-role.php)
 *
 * This function can be moved to another hook (if you find any more suitable), but without any requirements and more
 * information about about the task, I find this one just perfect. If the another hook doesn't provide item data,
 * similar code to one from payment-history-regenerate-role.php can be used
 */
add_action( 'edd_purchase_receipt_after_files', 'edd_set_user_role', 10, 2);