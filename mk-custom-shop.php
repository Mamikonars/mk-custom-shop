<?php
/*
Plugin Name: Mk Custom Shop
Description: Custom shop plugin (Test Plugin)
Version: 1.0
Author: Mamikon
Author URI: https://github.com/Mamikonars
License: GPLv2

*/
/* Copyright 2013 Brad Williams (email : brad@webdevstudios.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

define('MKCSHN', 'mk-custom-shop-name');

register_activation_hook( __FILE__ , 'mk_custom_shop_install' );
function mk_custom_shop_install() {
    //setup default option values
    $mk_custom_shop_options_arr = array(
    'currency_sign' => '$'
    );
    //save our default option values
    update_option( 'mk_custom_shop_options', $mk_custom_shop_options_arr );
}

// Action hook to initialize the plugin
add_action( 'init', 'mk_custom_shop_init' );
//Initialize
function mk_custom_shop_init() {
    //register the products custom post type
    $labels = array(
    'name' => __( 'Products', MKCSHN ),
    'singular_name' => __( 'Product', MKCSHN ),
    'add_new' => __( 'Add New', MKCSHN ),
    'add_new_item' => __( 'Add New Product', MKCSHN ),
    'edit_item' => __( 'Edit Product', MKCSHN ),
    'new_item' => __( 'New Product', MKCSHN ),
    'all_items' => __( 'All Products', MKCSHN ),
    'view_item' => __( 'View Product', MKCSHN ),
    'search_items' => __( 'Search Products', MKCSHN ),
    'not_found' => __( 'No products found', MKCSHN ),
    'not_found_in_trash' => __( 'No products found in Trash',
    MKCSHN ),
    'menu_name' => __( 'Products', MKCSHN )
 );

    $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' )
    );

    register_post_type( 'mk-custom-shop-prod', $args );
}

// Action hook to add the post products menu item
add_action( 'admin_menu', 'mk_custom_store_options_menu' );
//create the sub-menu
function mk_custom_store_options_menu() {
    add_options_page( __( 'MK Store Settings Page',
    MKCSHN ), __( 'MK Store Settings',
    MKCSHN ), 'manage_options', 'mk-store-settings',
    'mk_store_settings_page' );
}

//build the plugin settings page
function mk_store_settings_page() {

    //load the plugin options array
    $mk_custom_shop_options_arr = get_option( 'mk_custom_shop_options' );   
    //set the option array values to variables
    $mk_shop_inventory = ( ! empty( $mk_custom_shop_options_arr['show_inventory'] ) ) ?
    $mk_custom_shop_options_arr['show_inventory'] : '';
    $mk_shop_currency_sign = $mk_custom_shop_options_arr['currency_sign'];
    ?>
    <div class="wrap">
    <h2><?php _e( 'MK Store Options', MKCSHN ) ?></h2>
    <form method="post" action="options.php">
    <?php settings_fields( 'mk-custom-shop-settings-group' ); ?>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><?php _e( 'Show Product Inventory',
    MKCSHN ) ?></th>
    <td><input type="checkbox" name="mk_custom_shop_options[show_inventory]"
    <?php echo checked( $mk_shop_inventory, 'on' ); ?> /></td>
    </tr>
    <tr valign="top">
    <th scope="row"><?php _e( 'Currency Sign', MKCSHN ) ?></th>
    <td><input type="text" name="mk_custom_shop_options[currency_sign]"
    value="<?php echo esc_attr( $mk_shop_currency_sign ); ?>"
    size="1" maxlength="1" /></td>
    </tr>
    </table>
    <p class="submit">
    <input type="submit" class="button-primary"
    value="<?php _e( 'Save Changes', MKCSHN ); ?>" />
    </p>
    </form>
    </div>
    <?php
}

// Action hook to register the plugin option settings
add_action( 'admin_init', 'mk_custom_shop_register_settings' );
function mk_custom_shop_register_settings() {
    //register the array of settings
    register_setting( 'mk-custom-shop-settings-group',
    'mk_custom_shop_options', 'mk_custom_shop_sanitize_options' );
}
function mk_custom_shop_sanitize_options( $options ) {
    $options['show_inventory'] = ( ! empty( $options['show_inventory'] ) ) ?
    sanitize_text_field( $options['show_inventory'] ) : '';
    $options['currency_sign'] = ( ! empty( $options['currency_sign'] ) ) ?
    sanitize_text_field( $options['currency_sign'] ) : '';
 return $options;
}

//Action hook to register the Products meta box
add_action( 'add_meta_boxes', 'mk_custom_shop_register_meta_box' );
function mk_custom_shop_register_meta_box() {
    // create our custom meta box
    add_meta_box( 'mk-custom-shop-product-meta',
    __( 'Product Information', MKCSHN ),
    'mk_custom_shop_meta_box', 'mk-custom-shop-prod', 'side', 'default' );
}

//build product meta box
function mk_custom_shop_meta_box( $post ) {
    // retrieve our custom meta box values
    $mk_custom_shop_sku = get_post_meta( $post->ID, '_mk_custom_shop_product_sku', true );
    $mk_custom_shop_price = get_post_meta( $post->ID, '_mk_custom_shop_product_price', true );
    $hween_weight = get_post_meta( $post->ID, '_mk_custom_shop_product_weight', true );
    $hween_color = get_post_meta( $post->ID, '_mk_custom_shop_product_color', true );
    $hween_inventory = get_post_meta( $post->ID, '_mk_custom_shop_product_inventory',
    true );
    //nonce field for security
    wp_nonce_field( 'meta-box-save', MKCSHN );
   
    // display meta box form
    echo '<table>';
    echo '<tr>';
    echo '<td>' .__('Sku', MKCSHN).':</td>
    <td><input type="text" name="mk_custom_shop_product_sku"
    value="'.esc_attr( $mk_custom_shop_sku ).'" size="10"></td>';
    echo '</tr><tr>';
    echo '<td>' .__('Price', MKCSHN).':</td>
    <td><input type="text" name="mk_custom_shop_product_price"
    value="'.esc_attr( $mk_custom_shop_price ).'" size="5"></td>';
    echo '</tr><tr>';
    echo '<td>' .__('Weight', MKCSHN).':</td>
    <td><input type="text" name="mk_custom_shop_product_weight"
    value="'.esc_attr( $hween_weight ).'" size="5"></td>';
    echo '</tr><tr>';
    echo '<td>' .__('Color', MKCSHN).':</td>
    <td><input type="text" name="mk_custom_shop_product_color"
    value="'.esc_attr( $hween_color ).'" size="5"></td>';
    echo '</tr><tr>';
    echo '<td>Inventory:</td>
    <td><select name="mk_custom_shop_product_inventory"
    id="mk_custom_shop_product_inventory">
    <option value="In Stock"'
    .selected( $hween_inventory, 'In Stock', false ). '>'
    .__( 'In Stock', MKCSHN ). '</option>
    <option value="Backordered"'
    .selected( $hween_inventory, 'Backordered', false ). '>'
    .__( 'Backordered', MKCSHN ). '</option>
    <option value="Out of Stock"'
    .selected( $hween_inventory, 'Out of Stock', false ). '>'
    .__( 'Out of Stock', MKCSHN ). '</option>
    <option value="Discontinued"'
    .selected( $hween_inventory, 'Discontinued', false ). '>'
    .__( 'Discontinued', MKCSHN ). '</option>
    </select></td>';
    echo '</tr>';
    //display the meta box shortcode legend section
    echo '<tr><td colspan="2"><hr></td></tr>';
    echo '<tr><td colspan="2"><strong>'
    .__( 'Shortcode Legend', MKCSHN ).'</strong></td></tr>';
    echo '<tr><td>' .__( 'Sku', MKCSHN ) .':
    </td><td>[mkshop show=sku]</td></tr>';
    echo '<tr><td>' .__( 'Price', MKCSHN ).':
    </td><td>[mkshop show=price]</td></tr>';
    echo '<tr><td>' .__( 'Weight', MKCSHN ).':
    </td><td>[mkshop show=weight]</td></tr>';
    echo '<tr><td>' .__( 'Color', MKCSHN ).':
    </td><td>[mkshop show=color]</td></tr>';
    echo '<tr><td>' .__( 'Inventory', MKCSHN ).':
    </td><td>[mkshop show=inventory]</td></tr>';
    echo '</table>';
    }

    // Action hook to save the meta box data when the post is saved


add_action( 'save_post','mk_custom_shop_save_meta_box' );


//save meta box data
function mk_custom_shop_save_meta_box( $post_id ) {
    //verify the post type is for mk-custom-shop-prod Products and metadata has been posted
    if ( get_post_type( $post_id ) == 'mk-custom-shop-prod'
    && isset( $_POST['mk_custom_shop_product_sku'] ) ) {
    
    //if autosave skip saving data
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;
    //check nonce for security
    check_admin_referer( 'meta-box-save', MKCSHN );
    // save the meta box data as post metadata
    update_post_meta( $post_id, '_mk_custom_shop_product_sku',
    sanitize_text_field( $_POST['mk_custom_shop_product_sku'] ) );
    update_post_meta( $post_id, '_mk_custom_shop_product_price',
    sanitize_text_field( $_POST['mk_custom_shop_product_price'] ) );
    update_post_meta( $post_id, '_mk_custom_shop_product_weight',
    sanitize_text_field( $_POST['mk_custom_shop_product_weight'] ) );
    update_post_meta( $post_id, '_mk_custom_shop_product_color',
    sanitize_text_field( $_POST['mk_custom_shop_product_color'] ) );
    update_post_meta( $post_id, '_mk_custom_shop_product_inventory',
    sanitize_text_field( $_POST['mk_custom_shop_product_inventory'] ) );
 }

}

// Action hook to create the products shortcode
add_shortcode( 'mkshop', 'mk_custom_shope_shortcode' );
//create shortcode
function mk_custom_shope_shortcode( $atts, $content = null ) {
    global $post;

    extract( shortcode_atts( array(
    "show" => ''
    ), $atts ) );
    //load options array
    $mk_custom_shop_options_arr = get_option( 'mk_custom_shop_options' );
    if ( $show == 'sku') {

    $meta_show = get_post_meta( $post->ID, '_mk_custom_shop_product_sku', true );

    }elseif ( $show == 'price' ) {

    $meta_show = $mk_custom_shop_options_arr['currency_sign'].
    get_post_meta( $post->ID, '_mk_custom_shop_product_price', true );

    }elseif ( $show == 'weight' ) {

    $meta_show = get_post_meta( $post->ID,
    '_mk_custom_shop_product_weight', true );

    }elseif ( $show == 'color' ) {

    $meta_show = get_post_meta( $post->ID,
    '_mk_custom_shop_product_color', true );

    }elseif ( $show == 'inventory' ) {

    $meta_show = get_post_meta( $post->ID,
    '_mk_custom_shop_product_inventory', true );

    }
    //return the shortcode value to display
    return $meta_show;
    }

    // Action hook to create plugin widget
add_action( 'widgets_init', 'mk_custom_shop_register_widgets' );

//register the widget
function mk_custom_shop_register_widgets() {
	
    register_widget( 'mk_shop_widget' );
	
}

//mk_shop_widget class
class mk_shop_widget extends WP_Widget {

    //process our new widget
    function __construct() {
        parent::__construct(
            'mk-shop-widget', // Base ID
            'MK Shop Widget', // Name
            array( 'description' => esc_html__( 'A Foo Widget', MKCSHN ), )
        );
    }

    //build our widget settings form
    function form( $instance ) {
		
        $defaults = array( 
			'title'           => __( 'Products', MKCSHN ), 
			'number_products' => '3' );
		
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = $instance['title'];
        $number_products = $instance['number_products'];
        ?>
            <p><?php _e('Title', MKCSHN ) ?>: 
				<input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
            <p><?php _e( 'Number of Products', MKCSHN ) ?>: 
				<input name="<?php echo $this->get_field_name( 'number_products' ); ?>" type="text" value="<?php echo esc_attr( $number_products ); ?>" size="2" maxlength="2" />
			</p>
        <?php
    }

    //save our widget settings
    function update( $new_instance, $old_instance ) {
		
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number_products'] = absint( $new_instance['number_products'] );

        return $instance;
		
    }

    //display our widget
    function widget( $args, $instance ) {
        global $post;
		
        extract( $args );

        echo $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        $number_products = $instance['number_products'];

        if ( ! empty( $title ) ) { echo $before_title . esc_html( $title ) . $after_title; };

		//custom query to retrieve products
		$args = array(
			'post_type'			=>	'mk-custom-shop-prod',
			'posts_per_page'	=>	absint( $number_products )
		);
		
        $dispProducts = new WP_Query();
        $dispProducts->query( $args );
		
        while ( $dispProducts->have_posts() ) : $dispProducts->the_post();

            //load options array
            $mk_custom_shop_options_arr = get_option( 'mk_custom_shop_options' );

            //load custom meta values
            $hs_price = get_post_meta( $post->ID, '_mk_custom_shop_product_price', true );
            $hs_inventory = get_post_meta( $post->ID, '_mk_custom_shop_product_inventory', true );
            ?>
			<p>
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?> Product Information">
				<?php the_title(); ?>
				</a>
			</p>
			<?php
			echo '<p>' .__( 'Price', MKCSHN ). ': '.$mk_custom_shop_options_arr['currency_sign'] .$hs_price .'</p>';

            //check if Show Inventory option is enabled
            if ( $mk_custom_shop_options_arr['show_inventory'] ) {
				
				//display the inventory metadata for this product
                echo '<p>' .__( 'Stock', MKCSHN ). ': ' .$hs_inventory .'</p>';
				
            }
            echo '<hr>';

        endwhile;		

		wp_reset_postdata();

        echo $after_widget;		
    }
	
}