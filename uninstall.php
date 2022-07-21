<?php
// If uninstall/delete not called from WordPress then exit
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
 exit();
// Delete option from options table
delete_option( 'mk_custom_shop_options' );
?>