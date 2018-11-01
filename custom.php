<?php 
//added
add_role(
    'driver',
    __( 'Driver' ),
    array(
        'read'         => true,
        'edit_posts'   => true,
    )
);
add_role(
    'kitchen',
    __( 'Kitchen' ),
    array(
        'read'         => true,
        'edit_posts'   => true,
    )
);
add_action('admin_menu', 'remove_built_in_roles');
 
function remove_built_in_roles() {
    global $wp_roles;
 
    $roles_to_remove = array('subscriber', 'contributor', 'author', 'editor','superadmin');
 
    foreach ($roles_to_remove as $role) {
        if (isset($wp_roles->roles[$role])) {
            $wp_roles->remove_role($role);
		//somehere
        }
    }
}

function register_cpt_result() {

	$labels = array(
		'name' => __( 'Kitchen', 'Kitchen' ),
		'singular_name' => __( 'Kitchen', 'Kitchen' ),
		'add_new' => __( 'Add New', 'Kitchen' ),
		'add_new_item' => __( 'Add New Kitchen', 'Kitchen' ),
		'edit_item' => __( 'Edit Kitchen', 'Kitchen' ),
		'new_item' => __( 'New Kitchen', 'Kitchen' ),
		'view_item' => __( 'View Kitchen', 'Kitchen' ),
		'search_items' => __( 'Search Kitchen', 'Kitchen' ),
		'not_found' => __( 'No Kitchens found', 'Kitchen' ),
		'not_found_in_trash' => __( 'No Kitchen found in Trash', 'Kitchen' ),
		'parent_item_colon' => __( 'Parent Kitchen:', 'Kitchen' ),
		'menu_name' => __( 'Kitchen', 'Kitchen' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' =>  array( 'title' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'capabilities' => array('create_posts' => 'do_not_allow'),
		'map_meta_cap' => true,
	);

	register_post_type( 'kitchen', $args );
	
	$labels = array(
		'name' => __( 'Delivery', 'Delivery' ),
		'singular_name' => __( 'Delivery', 'Delivery' ),
		'add_new' => __( 'Add New', 'Delivery' ),
		'add_new_item' => __( 'Add New Delivery', 'Delivery' ),
		'edit_item' => __( 'Edit Delivery', 'Delivery' ),
		'new_item' => __( 'New Delivery', 'Delivery' ),
		'view_item' => __( 'View Delivery', 'Delivery' ),
		'search_items' => __( 'Search Delivery', 'Delivery' ),
		'not_found' => __( 'No Deliveries found', 'Delivery' ),
		'not_found_in_trash' => __( 'No Delivery found in Trash', 'Delivery' ),
		'parent_item_colon' => __( 'Parent Delivery:', 'Delivery' ),
		'menu_name' => __( 'Deliveries', 'Delivery' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' =>  array( 'title' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'capabilities' => array('create_posts' => 'do_not_allow'),
		'map_meta_cap' => true,
	);

	register_post_type( 'delivery', $args );
	
	$labels = array(
		'name' => __( 'Notification', 'Notification' ),
		'singular_name' => __( 'Notification', 'Notification' ),
		'add_new' => __( 'Add New', 'Notification' ),
		'add_new_item' => __( 'Add New Notification', 'Notification' ),
		'edit_item' => __( 'Edit Notification', 'Notification' ),
		'new_item' => __( 'New Notification', 'Notification' ),
		'view_item' => __( 'View Notification', 'Notification' ),
		'search_items' => __( 'Search Notification', 'Notification' ),
		'not_found' => __( 'No Notifications found', 'Notification' ),
		'not_found_in_trash' => __( 'No Notification found in Trash', 'Notification' ),
		'parent_item_colon' => __( 'Parent Notification:', 'Notification' ),
		'menu_name' => __( 'Notifications', 'Notification' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' =>  array( 'title' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'capabilities' => array('create_posts' => 'do_not_allow'),
		'map_meta_cap' => true,
	);

	register_post_type( 'Notification', $args );
}
add_action( 'init', 'register_cpt_result' );

if (!function_exists('getOrderDetailById')) {
    //to get full order details
    function getOrderDetailById($id, $fields = null, $filter = array()) {
        if (is_wp_error($id))
            return $id;
        // Get the decimal precession
        $dp = (isset($filter['dp'])) ? intval($filter['dp']) : 2;
        $order = wc_get_order($id); //getting order Object
        $order_data = array(
            'id' => $order->get_id(),
            'order_number' => $order->get_order_number(),
            'created_at' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'updated_at' => $order->get_date_modified()->date('Y-m-d H:i:s'),
            'completed_at' => !empty($order->get_date_completed()) ? $order->get_date_completed()->date('Y-m-d H:i:s') : '',
            'status' => $order->get_status(),
            'currency' => $order->get_currency(),
            'total' => wc_format_decimal($order->get_total(), $dp),
            'subtotal' => wc_format_decimal($order->get_subtotal(), $dp),
            'total_line_items_quantity' => $order->get_item_count(),
            'total_tax' => wc_format_decimal($order->get_total_tax(), $dp),
            'total_shipping' => wc_format_decimal($order->get_total_shipping(), $dp),
            'cart_tax' => wc_format_decimal($order->get_cart_tax(), $dp),
            'shipping_tax' => wc_format_decimal($order->get_shipping_tax(), $dp),
            'total_discount' => wc_format_decimal($order->get_total_discount(), $dp),
            'shipping_methods' => $order->get_shipping_method(),
            'order_key' => $order->get_order_key(),
            'payment_details' => array(
                'method_id' => $order->get_payment_method(),
                'method_title' => $order->get_payment_method_title(),
                'paid_at' => !empty($order->get_date_paid()) ? $order->get_date_paid()->date('Y-m-d H:i:s') : '',
            ),
            'billing_address' => array(
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
                'company' => $order->get_billing_company(),
                'address_1' => $order->get_billing_address_1(),
                'address_2' => $order->get_billing_address_2(),
                'city' => $order->get_billing_city(),
                'state' => $order->get_billing_state(),
                'formated_state' => WC()->countries->states[$order->get_billing_country()][$order->get_billing_state()], //human readable formated state name
                'postcode' => $order->get_billing_postcode(),
                'country' => $order->get_billing_country(),
                'formated_country' => WC()->countries->countries[$order->get_billing_country()], //human readable formated country name
                'email' => $order->get_billing_email(),
                'phone' => $order->get_billing_phone()
            ),
            'shipping_address' => array(
                'first_name' => $order->get_shipping_first_name(),
                'last_name' => $order->get_shipping_last_name(),
                'company' => $order->get_shipping_company(),
                'address_1' => $order->get_shipping_address_1(),
                'address_2' => $order->get_shipping_address_2(),
                'city' => $order->get_shipping_city(),
                'state' => $order->get_shipping_state(),
                'formated_state' => WC()->countries->states[$order->get_shipping_country()][$order->get_shipping_state()], //human readable formated state name
                'postcode' => $order->get_shipping_postcode(),
                'country' => $order->get_shipping_country(),
                'formated_country' => WC()->countries->countries[$order->get_shipping_country()] //human readable formated country name
            ),
            'note' => $order->get_customer_note(),
            'customer_ip' => $order->get_customer_ip_address(),
            'customer_user_agent' => $order->get_customer_user_agent(),
            'customer_id' => $order->get_user_id(),
            'view_order_url' => $order->get_view_order_url(),
            'line_items' => array(),
            'shipping_lines' => array(),
            'tax_lines' => array(),
            'fee_lines' => array(),
            'coupon_lines' => array(),
        );
        //getting all line items
	    //anotherhre
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $product_id = null;
            $product_sku = null;
            // Check if the product exists.
            if (is_object($product)) {
                $product_id = $product->get_id();
                $product_sku = $product->get_sku();
            }
            $order_data['line_items'][] = array(
                'id' => $item_id,
                'subtotal' => wc_format_decimal($order->get_line_subtotal($item, false, false), $dp),
                'subtotal_tax' => wc_format_decimal($item['line_subtotal_tax'], $dp),
                'total' => wc_format_decimal($order->get_line_total($item, false, false), $dp),
                'total_tax' => wc_format_decimal($item['line_tax'], $dp),
                'price' => wc_format_decimal($order->get_item_total($item, false, false), $dp),
                'quantity' => wc_stock_amount($item['qty']),
                'tax_class' => (!empty($item['tax_class']) ) ? $item['tax_class'] : null,
                'name' => $item['name'],
                'product_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product->get_parent_id() : $product_id,
                'variation_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product_id : 0,
                'product_url' => get_permalink($product_id),
                'product_thumbnail_url' => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail', TRUE)[0],
                'sku' => $product_sku,
                /*'meta' => wc_display_item_meta($item)*/
            );
        }
        //getting shipping
        foreach ($order->get_shipping_methods() as $shipping_item_id => $shipping_item) {
            $order_data['shipping_lines'][] = array(
                'id' => $shipping_item_id,
                'method_id' => $shipping_item['method_id'],
                'method_title' => $shipping_item['name'],
                'total' => wc_format_decimal($shipping_item['cost'], $dp),
            );
        }
        //getting taxes
        foreach ($order->get_tax_totals() as $tax_code => $tax) {
            $order_data['tax_lines'][] = array(
                'id' => $tax->id,
                'rate_id' => $tax->rate_id,
                'code' => $tax_code,
                'title' => $tax->label,
                'total' => wc_format_decimal($tax->amount, $dp),
                'compound' => (bool) $tax->is_compound,
            );
        }
        //getting fees
        foreach ($order->get_fees() as $fee_item_id => $fee_item) {
            $order_data['fee_lines'][] = array(
                'id' => $fee_item_id,
                'title' => $fee_item['name'],
                'tax_class' => (!empty($fee_item['tax_class']) ) ? $fee_item['tax_class'] : null,
                'total' => wc_format_decimal($order->get_line_total($fee_item), $dp),
                'total_tax' => wc_format_decimal($order->get_line_tax($fee_item), $dp),
            );
        }
        //getting coupons
        foreach ($order->get_items('coupon') as $coupon_item_id => $coupon_item) {
            $order_data['coupon_lines'][] = array(
                'id' => $coupon_item_id,
                'code' => $coupon_item['name'],
                'amount' => wc_format_decimal($coupon_item['discount_amount'], $dp),
            );
        }
        return array('order' => apply_filters('woocommerce_api_order_response', $order_data, $order, $fields));
    }
}

function delete_all_meta(){
	/*$post_id = array(348);
	foreach($post_id as $p){
		$myvals = get_post_meta($p);
		foreach($myvals as $key=>$val)  {
			delete_post_meta($p, $key);
		}
	}
	update_user_meta( 2, 'driver_availability', 'Free');
	update_user_meta( 3, 'driver_availability', 'Free');
	update_user_meta( 4, 'driver_availability', 'Free');
	update_user_meta( 5, 'driver_availability', 'Free');
	update_user_meta( 6, 'driver_availability', 'Free');
	update_user_meta( 8, 'driver_availability', 'Free');*/
}
add_action('init','delete_all_meta');

add_action( 'customize_register', 'cd_customizer_settings' );
function cd_customizer_settings( $wp_customize ) {
	$wp_customize->add_section( 'th_settings' , array(
		'title'      => 'Wolf Nutrition Settings',
		'priority'   => 30,
	) );
	
	$wp_customize->add_setting( 'toppaneltagline' , array(
		'default'     => '',
		'transport'   => 'postMessage',
	) );

	$wp_customize->add_control( 'toppaneltagline', array(
		'label' => 'Top Panel Tagline',
		'section'	=> 'th_settings',
		'type'	 => 'text',
	) );
	
	$wp_customize->add_setting( 'copyrighttext' , array(
		'default'     => '',
		'transport'   => 'postMessage',
	) );

	$wp_customize->add_control( 'copyrighttext', array(
		'label' => 'Copyright',
		'section'	=> 'th_settings',
		'type'	 => 'text',
	) );
	
	$wp_customize->add_setting( 'startaddress' , array(
		'default'     => '',
		'transport'   => 'postMessage',
	) );

	$wp_customize->add_control( 'startaddress', array(
		'label' => 'Driver Startaddress for Map',
		'section'	=> 'th_settings',
		'type'	 => 'text',
	) );
}

function update_order_address_request_post(){
	if(isset($_POST['address_update']) && $_POST['address_update'] != ''){
		$customer_id = get_current_user_id();
		$address = $_POST['address_update'].'#:'.$_POST['_billing_first_name'].'#:'.$_POST['_billing_last_name'].'#:'.$_POST['_billing_company'].'#:'.$_POST['_billing_address_1'].'#:'.$_POST['_billing_address_2'].'#:'.$_POST['_billing_city'].'#:'.$_POST['_billing_postcode'];		
		$timezone = date("Y-m-d h:m");
		$attempt = 1;
		$metas = get_post_meta(get_option( 'today_notification' ),'notification');
		$metas = array_reverse($metas);
		foreach($metas as $meta){
			$meta = explode(":#",$meta);
			$notetype = $meta[0];
			if($notetype == 'addressupdaterequest'){
				if($meta[1].$meta[3]== $customer_id.$_POST['address_update']){
					$attempt++;
				}
			}
		}		
		update_user_meta( $customer_id, 'order_address_update#:'.$_POST['address_update'].'#:'.$attempt, 1);
		add_post_meta( get_option( 'today_notification' ), 'notification', 'addressupdaterequest:#'.$customer_id.':#'.$address.':#'.$_POST['address_update'].':#'.$timezone.':#'.$attempt, false );
	}
}
add_action('init','update_order_address_request_post');

add_filter ( 'woocommerce_account_menu_items', 'remove_my_account_links' );
function remove_my_account_links( $menu_links ){
	unset( $menu_links['edit-address'] );
	return $menu_links;
}

add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');
add_action( "user_new_form", "custom_user_profile_fields" );

function custom_user_profile_fields( $user ) {
	$user_meta=get_userdata($user->data->ID);
	$user_roles=$user_meta->roles;
	$flag = 0;
	if($user_roles){
	foreach($user_roles as $user_role){
		if($user_role == 'customer'){
			$flag=1;
		}else if($user_role == 'driver'){
			$flag=2;
		}
	}}
	if($flag==1){
	?>
	<table class="form-table">
        <tr>
            <th>
                <label for="code"><?php _e( 'Flag Customer' ); ?></label>
            </th>
            <td>
				<select name="customer_flag" id="code">
					<option <?php if(!esc_attr( get_the_author_meta( 'customer_flag', $user->ID ) )) {echo "selected";}?> value="-">Select Option</option>
					<option <?php if(esc_attr( get_the_author_meta( 'customer_flag', $user->ID ) ) == "yes") {echo "selected";}?> value="yes">Yes</option>
					<option <?php if(esc_attr( get_the_author_meta( 'customer_flag', $user->ID ) ) == "no") {echo "selected";}?> value="no">No</option>
				</select>
            </td>
        </tr>
    </table>
<?php } ?>
    <style>
	.driverarea{display:none}
	.driverarea.active{display:block}
	</style>
	<?php 
	global $wpdb;
	$driver_groups = get_the_author_meta( 'driver_area', $user->ID );
	$driver_groups = explode(",",$driver_groups);
	?>
	<table class="form-table driverarea<?php if($flag==2) {echo "active";}?>">
        <tr>
            <th>
                <label for="code"><?php _e( 'Delivery Groups' ); ?></label>
            </th>
            <td>
				<?php
				$groups = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups');				
				foreach($groups as $group){
					if (in_array($group->group_id, $driver_groups)){
						$checked = 'checked';
					}else{
						$checked = '';
					}
					echo '<label style="margin-right:10px"><input name="group_list[]" '.$checked.' type="checkbox" value="'.$group->group_id.'"> '.$group->group_name.'</label>';
				}
				?>
            </td>
        </tr>
    </table>
<?php }

function save_custom_user_profile_fields($user_id){
    if(!current_user_can('manage_options'))
        return false;
	$group_str = '';
	if(!empty($_POST['group_list'])) {
		foreach($_POST['group_list'] as $group){
			$group_str .= $group.',';
		}
		$group_str = rtrim($group_str,",");
		update_user_meta($user_id, 'driver_area', $group_str);
	}
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');

add_action( 'personal_options_update', 'update_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );

function update_extra_profile_fields( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) )
        update_user_meta( $user_id, 'customer_flag', $_POST['customer_flag'] );
}

function new_contact_methods( $contactmethods ) {
    $contactmethods['Flag'] = 'Flag';
    return $contactmethods;
}
add_filter( 'user_contactmethods', 'new_contact_methods', 10, 1 );


function new_modify_user_table( $column ) {
    $column['Flag'] = 'Extra Care';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'Flag' :
			if(get_the_author_meta( 'customer_flag', $user_id ) == 'yes'){
				return '<img src="'.get_site_url().'/wp-content/uploads/2018/09/flag-map-marker_318-50576.png"/>';
			}else if(get_the_author_meta( 'customer_flag', $user_id ) == 'yes'){
				return '-';
			}else{
				return '-';
			}
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

/* Wolf Settings Settings Page */
class wolfsettings_Settings_Page {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wph_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_fields' ) );
	}
	public function wph_create_settings() {
		$page_title = 'Wolf Settings';
		$menu_title = 'Wolf Settings';
		$capability = 'manage_options';
		$slug = 'wolfsettings';
		$callback = array($this, 'wph_settings_content');
		$icon = 'dashicons-admin-settings';
		$position = 2;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
	}
	public function wph_settings_content() { ?>
		<div class="wrap">
			<h1>Wolf Settings</h1>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'wolfsettings' );
					do_settings_sections( 'wolfsettings' );
					submit_button();
				?>
			</form>
		</div> <?php
	}
	public function wph_setup_sections() {
		add_settings_section( 'wolfsettings_section', '', array(), 'wolfsettings' );
	}
	public function wph_setup_fields() {
		$fields = array(
			array(
				'label' => '',
				'id' => 'calendardata_27813',
				'type' => 'calendar',
				'section' => 'wolfsettings_section',
			),
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'wph_field_callback' ), 'wolfsettings', $field['section'], $field );
			register_setting( 'wolfsettings', $field['id'] );
		}
	}
	public function wph_field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
				case 'calendar':
				echo '<style>.loadersmall {border: 5px solid #f3f3f3;-webkit-animation: spin 1s linear infinite;animation: spin 1s linear infinite;border-top: 5px solid #555;border-radius: 50%;width: 50px;height: 50px;}.product-stock-wrap input{width:100%}.submitproductstock{margin-top:20px !important}#submit,#dialog{display:none}.fc-day.fc-widget-content.active{background-color:#ff6ea0}.form-table th{width:auto}</style><div id="calendar"></div>';
			?><div id="dialog"  title="&nbsp;">
			<div class="dialog-content"></div>
			<a class="submitproductstock button button-primary button-large" href="#">Update</a>
			</div>
<?php			
				/*printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
					$field['id'],
					$field['placeholder'],
					$value
					);*/
					break;
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}
}
new wolfsettings_Settings_Page();

function get_product_capacity(){
	$events = '';
	$options = wp_load_alloptions();
	foreach($options as $key=>$val){
		$checkKey = explode("#:", $key);
		if($checkKey[0]=='fullcalendar'){
			$time = strtotime($checkKey[1]);
			$newformat = date('Y-m-d',$time);
			$today = date('Y-m-d');
			if($newformat >= $today){
				$values = explode("#:",$val);
				$sum = 0;
				foreach($values as $value){
					$value = explode(",",$value);
					$sum = $sum + $value[1];
				}
				$events .= "{title:'Total Capacity: ".$sum."',start:'".$checkKey[1]."'},";
			}
		}
	}
	return rtrim($events,",");
}

add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
function load_admin_styles() {
	wp_enqueue_style( 'fullcalendar_min', get_template_directory_uri() . '/css/admin/fullcalendar.min.css');
	wp_enqueue_style( 'select2_min', get_template_directory_uri() . '/css/admin/select2.min.css');
	wp_enqueue_script(  'fullcalendar-moment', get_template_directory_uri().'/js/moment.min.js', array( 'jquery' ) );
	wp_enqueue_script(  'fullcalendar', get_template_directory_uri().'/js/fullcalendar.min.js', array( 'jquery' ) );
	wp_enqueue_script(  'select2', get_template_directory_uri().'/js/select2.min.js', array( 'jquery' ) );
	wp_register_script(  'fc-admin-js', get_template_directory_uri().'/js/admin-custom.js', array( 'jquery' ) );
	$translation_array = get_product_capacity();
	wp_localize_script( 'fc-admin-js', 'fcevents', $translation_array );
	wp_enqueue_script( 'fc-admin-js' );

}

function get_product_stock_on_date(){
    $selected_date = $_REQUEST['selected_date'];
	$option = 'fullcalendar#:'.$selected_date;
	$options = get_option($option);
	$options = explode("#:",$options);
	$the_query = new WP_Query( array(
		'post_type'=> 'product',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	$html = '<table class="product-stock-wrap wp-list-table widefat fixed striped posts"><tbody><tr><td>Product#</td><td>Name</td><td>Capacity</td></tr>';
	while ($the_query -> have_posts()) : $the_query -> the_post();
	$value = '';
	foreach($options as $option){
		$values = explode(",",$option);
		if($values[0]==get_the_ID() && $value==''){
			$value = $values[1];
		}
	}
	$html .= '<tr><td>'.get_the_ID().'</td><td>'.get_the_title().'</td><td><input data-id="'.get_the_ID().'"  type="number" value="'.$value.'"/></td><tr>';
	endwhile;
	wp_reset_postdata();
	$html .= '</tbody></table>';
	echo $html;
	exit;
}
add_action('wp_ajax_get_product_stock_on_date', 'get_product_stock_on_date');

function manage_product_stock(){
    $selected_date = 'fullcalendar#:'.$_REQUEST['selected_date'];
	$string = rtrim($_REQUEST['string'],"#:");
	update_option( $selected_date, $string );
	exit;
}
add_action('wp_ajax_manage_product_stock', 'manage_product_stock');

add_action('admin_footer', 'my_admin_footer_function');
function my_admin_footer_function() { ?>
	<script>
	jQuery(document).ready(function() {
		jQuery('#calendar').fullCalendar({
		/*defaultDate: '2018-03-12',*/
		editable: true,
		eventLimit: true,
		dayClick: function(date, jsEvent, view) {
		selected = new Date(date.format());
		today = new Date();
		if(today < selected){
			$('.fc-day.fc-widget-content').removeClass('active');
			$(this).addClass('active');
			$( "#dialog" ).attr('title','Product Stock('+date.format()+')');
			calendarStockData = $('#calendardata_27813').val();
			selectedDate = date.format();
			$( "#dialog .dialog-content" ).html('<div class="loadersmall"></div>');
			$( "#dialog" ).dialog({
				maxWidth:600,
                maxHeight: 500,
                width: 600,
                height: 500,
			});
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'get_product_stock_on_date' , selected_date : selectedDate, calendar_stock_data : calendarStockData }
			}).done(function( data ) {
				$('.submitproductstock').data('date',selectedDate);
				$( "#dialog .dialog-content" ).html(data);
			});
		}
	  },
	  events: [
            <?php echo get_product_capacity();?>
        ]
	});
	})
	</script>
	<script>
	jQuery(document).ready(function() {
		jQuery('.post-php.post-type-subscription .button.button-primary').click(function(e) {
			var freezeCheck = jQuery('.subscription_actions select').val();
			if(freezeCheck){
				if(freezeCheck == 'resume'){
				ajaxFlag = 1;
				}else if(freezeCheck == 'pause'){
					ajaxFlag = 1;
				}else{
					ajaxFlag = 0;
				}
				if(ajaxFlag == 1){
					subscriptionID = "<?php echo get_the_ID()?>";
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: {
							action: 'update_freeze',
							update_type: freezeCheck,
							subscription_id: subscriptionID
						}
					}).done(function(msg) {
						
					});
				}
			}
		});
	})
	</script>
<?php }

class WPDocs_Woocommerce_Order_Meta_Box {

    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }

    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_delivery'  ) );
        add_action( 'save_post',      array( $this, 'save_metabox_delivery' ), 10, 2 );
    }

    public function add_metabox_delivery() {
        add_meta_box(
            'my-meta-box',
            __( 'Manage Meal Duration', 'textdomain' ),
            array( $this, 'render_metabox_delivery' ),
            'shop_order',
            'advanced',
            'default'
        );
 
    }
	
 
    public function render_metabox_delivery( $post ) {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		$order_id = get_the_ID();
		$order_data = getOrderDetailById($order_id);
		$address = $order_data['order']['billing_address'];
		$address = $address['address_1'].' '.$address['address_2'].' '.$address['city'].' '.$address['formated_state'];
		echo '<div class="verify_address" style="display:none">'.$address.'</div>';
		echo '<div class="verify_address_order_id" style="display:none">'.$order_id.'</div>';
		$the_query = new WP_Query( array(
			'post_type'=> 'subscription',
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			));
		while ($the_query -> have_posts()) : $the_query -> the_post();
			$s = new Subscriptio;
			$subscription = $s->load_from_cache('subscriptions', get_the_ID());
			if (in_array($order_id, $subscription->all_order_ids)){
				foreach($subscription->all_order_ids as $soid){
					$parent = $soid;
				}
			}
		endwhile;
		wp_reset_postdata();
		if($parent == $order_id){
			$order = wc_get_order($order_id);?>
			<style>.loadersmall {border: 5px solid #f3f3f3;-webkit-animation: spin 1s linear infinite;animation: spin 1s linear infinite;border-top: 5px solid #555;border-radius: 50%;width: 10px;height: 10px;}.loading-wrap{display:inline-block;margin-left:10px}</style>
			<table class="wp-list-table widefat fixed striped posts">
				<tr>
					<td>Product Name</td>
					<td>Meal Start Date</td>
					<td>Meal End Date</td>
					<td></td>
				</tr>
			<?php foreach ($order->get_items() as $item_id => $item) {
			$startdate = wc_get_order_item_meta($item_id , 'Meal Start Date');
			$enddate = wc_get_order_item_meta($item_id , 'Meal End Date');
			$product_id = $item->get_product_id();
			?>
				<tr>
					<td><?php echo $item->get_name();?></td>
					<td><input type="text" class="startdate datepickerstart" value="<?php echo $startdate;?>"/></td>
					<td><input type="text" class="enddate datepickerend" value="<?php echo $enddate;?>"/></td>
					<td><a data-product="<?php echo $product_id;?>" data-order_id="<?php echo $order_id;?>" data-id="<?php echo $item_id;?>" class="updatedates button button-primary button-large" href="#">Update</a><div class="loading-wrap"></div></td>
				</tr>
			<?php }
			echo '</table>';
		}else{
			echo 'Manage Meal duration <a href="'.get_site_url().'/wp-admin/post.php?post='.$parent.'&action=edit">here</a>.';
		}
    }
 
    public function save_metabox_delivery( $post_id, $post ) {
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';
        if ( ! isset( $nonce_name ) ) {return;}
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {return;}
        if ( ! current_user_can( 'edit_post', $post_id ) ) {return;}
        if ( wp_is_post_autosave( $post_id ) ) {return;}
        if ( wp_is_post_revision( $post_id ) ) {return;}
    }
}

new WPDocs_Woocommerce_Order_Meta_Box();

class WPDocs_Woocommerce_Address_Meta_Box {

    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }

    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_address'  ) );
        add_action( 'save_post',      array( $this, 'save_metabox_address' ), 10, 2 );
    }

    
	
	public function add_metabox_address() {
        add_meta_box(
            'my-meta-box-address',
            __( 'Verify Address', 'textdomain' ),
            array( $this, 'render_metabox_address' ),
            'shop_order',
            'advanced',
            'default'
        );
 
    }
	
	public function render_metabox_address( $post ) {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );?>
		<style>
			#map {height: 100%;}
			.address_error {color: red;}
			.verify_address_btn{margin-top:20px}
		</style>
		<div id="map_canvas" style="width:100%; height:300px"></div>
		<div class="address_error" style="display:none"></div>		
		<div class="verify_address_btn">
			<select class="proper_address">
			<option value="0">Select option</option>
			<option value="1">Proper Address</option>
			<option value="2">Not Proper Address</option>
			</select>
			<a class="updateaddress button button-primary button-large" href="#">Update</a>
		</div>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDaicNioc15Z8dG1oZuSByRN0c2tl54Zls"></script>
    <?php }
 
    public function save_metabox_address( $post_id, $post ) {
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';
        if ( ! isset( $nonce_name ) ) {return;}
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {return;}
        if ( ! current_user_can( 'edit_post', $post_id ) ) {return;}
        if ( wp_is_post_autosave( $post_id ) ) {return;}
        if ( wp_is_post_revision( $post_id ) ) {return;}
    }
}
new WPDocs_Woocommerce_Address_Meta_Box();

function updatedates_order(){
	global $wpdb;
	$order_id = $_REQUEST['order_id'];
	$product_id = $_REQUEST['product_id'];
    $item_id = $_REQUEST['item_id'];
	$startdate = $_REQUEST['startdate'];
	$enddate = $_REQUEST['enddate'];
	$order = wc_get_order($order_id);
	wc_update_order_item_meta($item_id, 'Meal Start Date', $startdate);
	wc_update_order_item_meta($item_id, 'Meal End Date', $enddate);
	clean_post_cache( $order->get_id() );
    wc_delete_shop_order_transients( $order );
    wp_cache_delete( 'order-items-' . $order->get_id(), 'orders' );
	$order = getOrderDetailById($order_id);
	$line_items = $order['order']['line_items'];
	foreach($line_items as $line_item){
		if($line_item['product_id'] == $product_id){
			$item_id = $line_item['id'];
			break;
		}
	}
	$startdate = wc_get_order_item_meta($item_id, 'Meal Start Date', true);
	$enddate = wc_get_order_item_meta($item_id, 'Meal End Date', true);
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_postmeta where meta_key="last_order_id" and meta_value="'.$order_id.'"');
	if($results){
		foreach($results as $result){
			$post_id = $result->post_id;
			$product = $wpdb->get_results ( 'SELECT * FROM  wp_postmeta where meta_key="product_id" and meta_value="'.$product_id.'" and post_id="'.$result->post_id.'"');
			if($product[0]->meta_value==$product_id){
				$subscription_id = $product[0]->post_id;
				break;
			}
		}
		if($subscription_id){
			$results = $wpdb->get_results ( 'SELECT * FROM  wp_subscriptio_scheduled_events where subscription_id="'.$subscription_id.'"');
			$expiration = '';
			$payment = '';
			$order = '';
			$durations = $wpdb->get_results ( 'SELECT * FROM  wp_postmeta where post_id="'.$post_id.'" and (meta_key="price_time_value" or meta_key="price_time_unit")');
			foreach($durations as $duration){
				if($duration->meta_key == 'price_time_unit'){
					$unit = $duration->meta_value;
				}else if($duration->meta_key == 'price_time_value'){
					$value = $duration->meta_value;
				}
			}
			foreach($results as $result){
				if($result->event_key == 'expiration'){
					$expiration = $result->event_timestamp;
					$time = date("h:i a",$expiration);
					$date = $enddate.' '.$time;
					$actual = strtotime($date);
					$datediff = $expiration - $actual;
					$expiration = date('m/d/Y h:i a', $expiration - $datediff);
					$expiration = strtotime($expiration);
					$expiration_readable = date('Y-m-d h:i:s', $expiration);
					$event = $result->event_id;
					$wpdb->update('wp_subscriptio_scheduled_events', array('event_timestamp'=>$expiration), array('event_id'=>$event));
					$wpdb->update('wp_postmeta', array('meta_value'=>$expiration), array('post_id'=>$subscription_id, 'meta_key'=>'expires'));
					$wpdb->update('wp_postmeta', array('meta_value'=>$expiration_readable), array('post_id'=>$subscription_id, 'meta_key'=>'expires_readable'));
					$started = $startdate.' '.$time;
					$started = strtotime($started);
					$started_readable = date('Y-m-d h:i:s', $started);
					$wpdb->update('wp_postmeta', array('meta_value'=>$started), array('post_id'=>$subscription_id, 'meta_key'=>'started'));
					$wpdb->update('wp_postmeta', array('meta_value'=>$started_readable), array('post_id'=>$subscription_id, 'meta_key'=>'started_readable'));
				}else if($result->event_key == 'payment'){
					$payment = $result->event_timestamp;
					$time = date("h:i a",$payment);
					$date = $startdate.' '.$time;
					$actual = strtotime($date);
					$event = $result->event_id;
					if($unit=='week'){
						$payment_due = strtotime("+7 day", $actual);
					}else if($unit=='month'){
						$payment_due = strtotime("+1 month", $actual);
					}
					$payment_due_readable = date('Y-m-d h:i:s', $payment_due);
					$wpdb->update('wp_subscriptio_scheduled_events', array('event_timestamp'=>$payment_due), array('event_id'=>$event));
					$wpdb->update('wp_postmeta', array('meta_value'=>$payment_due), array('post_id'=>$subscription_id, 'meta_key'=>'payment_due'));
					$wpdb->update('wp_postmeta', array('meta_value'=>$payment_due_readable), array('post_id'=>$subscription_id, 'meta_key'=>'payment_due_readable'));
				}else if($result->event_key == 'order'){
					$renewal_order = strtotime("-1 day", $payment_due);
					$event = $result->event_id;
					$wpdb->update('wp_subscriptio_scheduled_events', array('event_timestamp'=>$renewal_order), array('event_id'=>$event));
				}
			}
		}
	}
	exit;
}
add_action('wp_ajax_updatedates_order', 'updatedates_order');

function create_dwb_menu() {
	global $wp_admin_bar;
	$counter = $_SESSION['freeze_request_total'];
	$menu_id = 'dwb';
	$title = 'Freeze/Unfreeze Request <span style="display:inline-block;background-color:red;line-height:20px;width:20px;border-radius:50%;text-align:center">'.$counter.'</span>';
	$wp_admin_bar->add_menu(array('id' => $menu_id,'parent' => 'top-secondary', 'title' => __($title), 'href' => 'edit.php?post_type=subscription'));
}
add_action('admin_bar_menu', 'create_dwb_menu', 5000);

function get_freeze_request_total(){
	$counter = 0;
	$the_query = new WP_Query( array(
		'post_type'=> 'subscription',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	$order_ids = '';
	while ($the_query -> have_posts()) : $the_query -> the_post();
		if(get_post_meta( get_the_ID(), 'freeze' , true ) == 1 or get_post_meta( get_the_ID(), 'freeze' , true ) == 3){
			$counter++;
		}
	endwhile;
	wp_reset_postdata();
	$_SESSION['freeze_request_total'] = $counter;
}
add_action('init','get_freeze_request_total');

function subscription_columns_head($defaults) {
    $defaults['freeze_request'] = 'Freeze/Unfreeze Request';
    return $defaults;
}
add_filter('manage_subscription_posts_columns', 'subscription_columns_head');

function subscription_columns_content($column_name, $post_ID) {
    if ($column_name == 'freeze_request') {
		if(get_post_meta( $post_ID, 'freeze' , true ) == 1){
			echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_ID.'&action=edit" style="color:red">Freeze Requested</a>';
		}else if(get_post_meta( $post_ID, 'freeze' , true ) == 2){
			echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_ID.'&action=edit" style="color:red">Freezed</a>';
		}else if(get_post_meta( $post_ID, 'freeze' , true ) == 3){
			echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_ID.'&action=edit" style="color:red">Unfreeze Requested</a>';
		}else {
			echo "-";
		}
    }
}
add_action('manage_subscription_posts_custom_column', 'subscription_columns_content', 10, 2);

function freezerequest(){
	$subscription_id = $_REQUEST['subscription_id'];
	$request_method = $_REQUEST['request_method'];
	if($request_method == 'Freeze'){
		update_post_meta( $subscription_id, 'freeze', '1' );
	}else if($request_method == 'Unfreeze'){
		update_post_meta( $subscription_id, 'freeze', '3' );
	}
}
add_action('wp_ajax_nopriv_freezerequest', 'freezerequest');
add_action('wp_ajax_freezerequest', 'freezerequest');

function freezecontent(){
	$subscription_id = $_REQUEST['subscription_id'];
	$str = '<dt>Actions:</dt><dd class="freeze-content">';
	if(get_post_meta( $subscription_id, 'freeze' , true ) == 1){
		$str .= 'Freeze Request Sent for Approval.';
	}else if(get_post_meta( $subscription_id, 'freeze' , true ) == 2){
		$str .= '<a href="#" id="unfreeze_subscription" class="button">Unfreeze Subscription</a>';
	}else if(get_post_meta( $subscription_id, 'freeze' , true ) == 3){
		$str .= 'Unfreeze Request Sent for Approval.';
	}else{
		$str .= '<a href="#" id="freeze_subscription" class="button">Freeze Subscription</a>';
	}
	$str .= '</dd>';
	echo $str;
	wp_die();
}
add_action('wp_ajax_nopriv_freezecontent', 'freezecontent');
add_action('wp_ajax_freezecontent', 'freezecontent');

function update_freeze(){
    $update_type = $_REQUEST['update_type'];
    $subscription_id = $_REQUEST['subscription_id'];
	if($update_type == 'pause'){
		update_post_meta( $subscription_id, 'freeze', '2' );
	}else if($update_type == 'resume'){
		update_post_meta( $subscription_id, 'freeze', '4' );
	}
	exit;
}
add_action('wp_ajax_update_freeze', 'update_freeze');

function add_delivery_areas_setting_tab() {
   $current_tab = ( $_GET['tab'] == 'delivery_areas' ) ? 'nav-tab-active' : '';
   echo '<a href="admin.php?page=wc-settings&amp;tab=delivery_areas" class="nav-tab '.$current_tab.'">'.__( "Areas", "domain" ).'</a>';
}
add_action( 'woocommerce_settings_tabs', 'add_delivery_areas_setting_tab' );

function add_delivery_groups_setting_tab() {
   $current_tab = ( $_GET['tab'] == 'delivery_groups' ) ? 'nav-tab-active' : '';
   echo '<a href="admin.php?page=wc-settings&amp;tab=delivery_groups" class="nav-tab '.$current_tab.'">'.__( "Groups", "domain" ).'</a>';
}
add_action( 'woocommerce_settings_tabs', 'add_delivery_groups_setting_tab' );

function wk_add_delivery_groups_tab_content() {
	global $wpdb;?>
   <style>
   .woocommerce-save-button{display:none !important}  .update_group_btn.woocommerce-save-button,.delete_group.woocommerce-save-button,.edit_group.woocommerce-save-button,#add_group.woocommerce-save-button,#create_group.woocommerce-save-button{display:inline-block !important}
   .create_group{border:2px solid #555;max-width:700px}
   .woocommerce table.form-table th {position: relative;padding-left: 15px;}
   .error{font-weight:bold;color:#ff4c4c;display:none;padding:5px 0px}
   .titledesc h2{margin:0px;color:#fff}
   .table-head{background-color:#000}
   .add_group{display:none;margin-bottom:20px}
   .success_msg{font-weight:bold;border:1px solid #ccc;padding:10px 20px;display:none;border-top:2px solid #4ca64c;min-width:500px}
   .delete_group_form{border:2px solid #555;max-width:700px;margin-top:20px;display:none;}
   .add_group.active,.delete_group_form.active{display:block}
   .custom_pagination {display: inline-block;margin-top:20px}
	.custom_pagination a {border: 1px solid #000;color: black;display: inline-block;padding: 8px 16px;text-decoration: none;margin: 0px 1px;}
	.custom_pagination a.active {background-color: #000;color: #fff;}
	.custom_pagination a:hover:not(.active) {background-color: #000;color:#fff}
	.center{text-align:center}
   a.close_box {text-decoration: none;float: right;margin-right: 10px;display: inline-block;padding: 5px 10px;color: #000;background: #fff;}
   </style>
   <p class="success_msg"></p>
   <h2>Manage Groups</h2>
   <p><a href="javascript:void(0)" id="add_group" class="button-primary woocommerce-save-button">Add New Group</a></p>
   <div class="add_group">
	<table class="form-table create_group">
		<tbody>
			<tr valign="top" class="table-head">
				<th scope="row" class="titledesc">
					<h2>New Group</h2>
				</th>
				<td class="forminp forminp-text">
					<a class="close_box" href="#">X</a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_decimal_sep">Group Name</label>
				</th>
				<td class="forminp forminp-text">
					<input type="text" placeholder="Group Name" class="groupname" />
					<span class="error">Group name required.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_num_decimals">Areas</label>
				</th>
				<td class="forminp forminp-number">
					<select class="groupareas" name="areas[]" multiple="multiple">
					
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<a href="javascript:void(0)" id="create_group" class="button-primary woocommerce-save-button">Create Group</a>
				</th>
				<td class="forminp forminp-number">
				
				</td>
			</tr>
		</tbody>
	</table>
   </div>
   
   <div class="all_group">
	
   </div>
   
   <div class="delete_group_form">
	<table class="form-table">
		<tbody>
			<tr valign="top" class="table-head">
				<th scope="row" class="titledesc">
					<h2>Update Group - <span></span></h2>
				</th>
				<td class="forminp forminp-text">
					<a class="close_box" href="#">X</a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_decimal_sep">Group Name</label>
				</th>
				<td class="forminp forminp-text">
					<input type="hidden" class="update_group_id" value=""/>
					<input type="text" placeholder="Group Name" class="updategroupname" />
					<span class="error">Group name required.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_num_decimals">Areas</label>
				</th>
				<td class="forminp forminp-number">
					<select class="updategroupareas" name="areas[]" multiple="multiple">
					
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<a href="javascript:void(0)" class="update_group_btn button-primary woocommerce-save-button">Update Group</a>
				</th>
				<td class="forminp forminp-number">
					
				</td>
			</tr>
		</tbody>
	</table>
   </div>
   
<?php }
add_action( 'woocommerce_settings_delivery_groups', 'wk_add_delivery_groups_tab_content' );

function wk_add_delivery_areas_tab_content() {?>
   <style>
   .woocommerce-save-button{display:none !important}  .update_area_btn.woocommerce-save-button,.delete_area.woocommerce-save-button,.edit_area.woocommerce-save-button,#add_group.woocommerce-save-button,#create_area.woocommerce-save-button{display:inline-block !important}
   .create_group{border:2px solid #555;max-width:700px}
   .woocommerce table.form-table th {position: relative;padding-left: 15px;}
   .error{font-weight:bold;color:#ff4c4c;display:none;padding:5px 0px}
   .titledesc h2{margin:0px;color:#fff}
   .table-head{background-color:#000}
   .add_group{display:none;margin-bottom:20px}
   .success_msg{font-weight:bold;border:1px solid #ccc;padding:10px 20px;display:none;border-top:2px solid #4ca64c;min-width:500px}
   .delete_group_form{border:2px solid #555;max-width:700px;margin-top:20px;display:none;}
   .add_group.active,.delete_group_form.active{display:block}
   .custom_pagination {display: inline-block;margin-top:20px}
	.custom_pagination a {border: 1px solid #000;color: black;display: inline-block;padding: 8px 16px;text-decoration: none;margin: 0px 1px;}
	.custom_pagination a.active {background-color: #000;color: #fff;}
	.custom_pagination a:hover:not(.active) {background-color: #000;color:#fff}
	.center{text-align:center}
	a.close_box {text-decoration: none;float: right;margin-right: 10px;display: inline-block;padding: 5px 10px;color: #000;background: #fff;}
   </style>
   <p class="success_msg"></p>
   <h2>Manage Areas</h2>
   <p><a href="javascript:void(0)" id="add_group" class="button-primary woocommerce-save-button">Add New Area</a></p>
   <div class="add_group">
	<table class="form-table create_group">
		<tbody>
			<tr valign="top" class="table-head">
				<th scope="row" class="titledesc">
					<h2>New Area</h2>
				</th>
				<td class="forminp forminp-text">
					<a class="close_box" href="#">X</a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_decimal_sep">Area Name</label>
				</th>
				<td class="forminp forminp-text">
					<input type="text" placeholder="Area Name" class="areaname" />
					<span class="error">Area name required.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<a href="javascript:void(0)" id="create_area" class="button-primary woocommerce-save-button">Create Area</a>
				</th>
				<td class="forminp forminp-number">
					
				</td>
			</tr>
		</tbody>
	</table>
   </div>
   
   <div class="all_area">
	
   </div>
   
   <div class="delete_group_form">
	<table class="form-table">
		<tbody>
			<tr valign="top" class="table-head">
				<th scope="row" class="titledesc">
					<h2>Update Area - <span></span></h2>
				</th>
				<td class="forminp forminp-text">
					<a class="close_box" href="#">X</a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_price_decimal_sep">Area Name</label>
				</th>
				<td class="forminp forminp-text">
					<input type="hidden" class="update_area_id" value=""/>
					<input type="text" placeholder="Area Name" class="updateareaname" />
					<span class="error">Area name required.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<a href="javascript:void(0)" class="update_area_btn button-primary woocommerce-save-button">Update Area</a>
				</th>
				<td class="forminp forminp-number">
					
				</td>
			</tr>
		</tbody>
	</table>
   </div>
   
<?php }
add_action( 'woocommerce_settings_delivery_areas', 'wk_add_delivery_areas_tab_content' );

function update_checkout_areas(){
	global $wpdb;
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas');
	$concatenate_area = array();
	foreach($results as $result){
		$area = $result->area_name;
		$concatenate_area[$area] = $area;
	}
	$fields = get_option('wc_fields_billing');
	$fields['billing_customer_area']['options'] = $concatenate_area;
	update_option('wc_fields_billing',$fields);
}

function create_delivery_group(){
	global $wpdb;
    $group_name = $_REQUEST['group_name'];
    $group_areas = $_REQUEST['group_areas'];
	$concatenate_area = '';
	foreach($group_areas as $grouparea){
		$concatenate_area .= $grouparea.', ';
	}
	$group_areas = rtrim($concatenate_area,', ');
	$wpdb->insert('wp_delivery_groups', array('area_id' => $group_areas,'group_name' => $group_name));
	exit;
}
add_action('wp_ajax_create_delivery_group', 'create_delivery_group');

function create_delivery_area(){
	global $wpdb;
    $area_name = $_REQUEST['area_name'];
	$wpdb->insert('wp_delivery_areas', array('area_name' => $area_name));
	update_checkout_areas();
	exit;
}
add_action('wp_ajax_create_delivery_area', 'create_delivery_area');

function add_group_area(){
	global $wpdb;
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas');
	$groups = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups');
	$occupied_areas = array();
	foreach($groups as $group){
		$groupareas = explode(", ",$group->area_id);
		foreach($groupareas as $grouparea){
			$occupied_areas[] = $grouparea;
		}
	}
	$str = '';
	foreach($results as $result){
		if (!in_array($result->id, $occupied_areas)){
			$str .= '<option value="'.$result->id.'">'.$result->area_name.'</option>';
		}
	}
	echo $str;
	exit;
}
add_action('wp_ajax_add_group_area', 'add_group_area');

function get_delivery_group(){
	global $wpdb;
	$group_per_page = 10;
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups');
	$foundgroups = $wpdb->num_rows;
	$total_pages = ceil($foundgroups / $group_per_page);
	if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'prev'){
		if($_REQUEST['current'] == 1){
			$page = 1;
		}else{
			$page = $_REQUEST['current'] - 1;
		}
	}else if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'next'){
		if($_REQUEST['current'] == $total_pages){
			$page = $total_pages;
		}else{
			$page = $_REQUEST['current'] + 1;
		}
	}else if(isset($_REQUEST['page']) and ($_REQUEST['page'] != 'prev' or $_REQUEST['page'] != 'next')){
		$page = $_REQUEST['page'];
	}else{
		$page = 1;
	}
	$group = $foundgroups.' Areas found.<br><br><table class="wp-list-table widefat fixed striped posts"><tr><td>GroupID#</td><td>Group Name</td><td>Areas</td><td>Actions</td></tr>';
	$counter = 1;
	if($page == 1){
		$group_skipper = 0;
	}else{
		$group_skipper = ($page-1)*$group_per_page;
	}
	foreach($results as $result){
		if($group_skipper==0){
			$groupname = $result->group_name;
			$groupareas = explode(", ",$result->area_id);
			$concatenate_area = '';
			foreach($groupareas as $grouparea){
				$area = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas where id="'.$grouparea.'"');
				$area = $area[0]->area_name;
				$concatenate_area .= $area.', ';
			}
			$concatenate_area =rtrim($concatenate_area,', ');
			$group .= '<tr><td>'.$result->group_id.'</td><td>'.$groupname.'</td><td>'.$concatenate_area.'</td><td><a href="javascript:void(0)" data-id="'.$result->group_id.'" class="button-primary edit_group woocommerce-save-button">Edit Group</a><a href="javascript:void(0)" data-id="'.$result->group_id.'" class="button-primary delete_group woocommerce-save-button">Delete Group</a></td></tr>';
			if($counter == $group_per_page){
				break;
			}else{
				$counter++;
			}
		}else{
			$group_skipper--;
		}
	}
	$group .= '</table><div class="center"><div class="custom_pagination"><a data-current="'.$page.'" data-page="prev" href="#">&laquo;</a>';
	for($i=1;$i<=$total_pages;$i++){
		if($page == $i){
			$group .= '<a data-current="'.$page.'" href="#" class="active" data-page="'.$i.'">'.$i.'</a>';
		}else{
			$group .= '<a data-current="'.$page.'" href="#" data-page="'.$i.'">'.$i.'</a>';
		}
	}
	$group .= '<a data-current="'.$page.'" data-page="next" href="#">&raquo;</a></div></div>';
	echo $group;
	exit;
}
add_action('wp_ajax_get_delivery_group', 'get_delivery_group');

function get_delivery_area(){
	global $wpdb;
	$area_per_page = 10;
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas');
	$foundarea = $wpdb->num_rows;
	$total_pages = ceil($foundarea / $area_per_page);
	if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'prev'){
		if($_REQUEST['current'] == 1){
			$page = 1;
		}else{
			$page = $_REQUEST['current'] - 1;
		}
	}else if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'next'){
		if($_REQUEST['current'] == $total_pages){
			$page = $total_pages;
		}else{
			$page = $_REQUEST['current'] + 1;
		}
	}else if(isset($_REQUEST['page']) and ($_REQUEST['page'] != 'prev' or $_REQUEST['page'] != 'next')){
		$page = $_REQUEST['page'];
	}else{
		$page = 1;
	}
	$str = $foundarea.' Areas found.<br><br><table class="wp-list-table widefat fixed striped posts"><tr><td>AreaID#</td><td>Area Name</td><td>Group</td><td>Actions</td></tr>';
	$counter = 1;
	if($page == 1){
		$area_skipper = 0;
	}else{
		$area_skipper = ($page-1)*$area_per_page;
	}
	foreach($results as $result){
		if($area_skipper==0){
			$group_name = '-';
			$groups = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups');
			foreach($groups as $group){
				$areas = explode(", ",$group->area_id);
				if($areas){
					foreach($areas as $area){
						if($area == $result->id){
							$group_name = $group->group_name;
						}
					}
				}
			}
			$str .= '<tr><td>'.$result->id.'</td><td>'.$result->area_name.'</td><td>'.$group_name.'</td><td><a href="javascript:void(0)" data-id="'.$result->id.'" class="button-primary edit_area woocommerce-save-button">Edit Area</a><a href="javascript:void(0)" data-id="'.$result->id.'" class="button-primary delete_area woocommerce-save-button">Delete Area</a></td></tr>';
			if($counter == $area_per_page){
				break;
			}else{
				$counter++;
			}
		}else{
			$area_skipper--;
		}
	}
	$str .= '</table><div class="center"><div class="custom_pagination"><a data-current="'.$page.'" data-page="prev" href="#">&laquo;</a>';
	for($i=1;$i<=$total_pages;$i++){
		if($page == $i){
			$str .= '<a data-current="'.$page.'" href="#" class="active" data-page="'.$i.'">'.$i.'</a>';
		}else{
			$str .= '<a data-current="'.$page.'" href="#" data-page="'.$i.'">'.$i.'</a>';
		}
	}
	$str .= '<a data-current="'.$page.'" data-page="next" href="#">&raquo;</a></div></div>';
	echo $str;
	exit;
}
add_action('wp_ajax_get_delivery_area', 'get_delivery_area');

function delete_delivery_group(){
	global $wpdb;
	$group_id = $_REQUEST['group_id'];
	$wpdb->delete( 'wp_delivery_groups', array( 'group_id' => $group_id ) );
	exit;
}
add_action('wp_ajax_delete_delivery_group', 'delete_delivery_group');

function delete_delivery_area(){
	global $wpdb;
    $area_id = $_REQUEST['area_id'];
	$wpdb->delete( 'wp_delivery_areas', array( 'id' => $area_id ) );
	update_checkout_areas();
	exit;
}
add_action('wp_ajax_delete_delivery_area', 'delete_delivery_area');

function update_delivery_group(){
    global $wpdb;
	$group_id = $_REQUEST['group_id'];
	$group_name = $_REQUEST['group_name'];
	$group_areas = $_REQUEST['group_areas'];
	$concatenate_area = '';
	foreach($group_areas as $grouparea){
		$concatenate_area .= $grouparea.', ';
	}
	$group_areas = rtrim($concatenate_area,', ');
	$wpdb->update('wp_delivery_groups', array('group_name'=>$group_name,'area_id'=>$group_areas), array('group_id'=>$group_id));
	exit;
}
add_action('wp_ajax_update_delivery_group', 'update_delivery_group');

function update_delivery_area(){
    global $wpdb;
	$area_id = $_REQUEST['area_id'];
	$area_name = $_REQUEST['area_name'];
	$wpdb->update('wp_delivery_areas', array('area_name'=>$area_name), array('id'=>$area_id));
	update_checkout_areas();
	exit;
}
add_action('wp_ajax_update_delivery_area', 'update_delivery_area');

function get_delivery_area_by_id(){
	global $wpdb;
    $area_id = $_REQUEST['area_id'];
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas where id="'.$area_id.'"');
	echo $results[0]->area_name;
	exit;
}
add_action('wp_ajax_get_delivery_area_by_id', 'get_delivery_area_by_id');

function get_delivery_group_by_id(){
	global $wpdb;
    $group_id = $_REQUEST['group_id'];
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups where group_id="'.$group_id.'"');
	$selected_areas = explode(", ",$results[0]->area_id);
	$groupareas = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas');
	$groups = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups');
	$occupied_areas = array();
	foreach($groups as $group){
		$groupareas1 = explode(", ",$group->area_id);
		foreach($groupareas1 as $grouparea){
			if(!in_array($grouparea, $selected_areas)){
				$occupied_areas[] = $grouparea;
			}
		}
	}	
	foreach($groupareas as $grouparea){
		if (!in_array($grouparea->id, $occupied_areas)){
			if(in_array($grouparea->id, $selected_areas)){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			$str .= '<option '.$selected.' value="'.$grouparea->id.'">'.$grouparea->area_name.'</option>';
		}
	}
	echo $results[0]->group_name.'#:'.$str;
	exit;
}
add_action('wp_ajax_get_delivery_group_by_id', 'get_delivery_group_by_id');

function my_admin_delivery_groups_function() { ?>
	<script>
	jQuery(document).ready(function() {
		
		jQuery('.groupareas').select2({
			 placeholder: "Select Area"
		});
		
		jQuery('.updategroupareas').select2({
			 placeholder: "Select Area"
		});
		
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'get_delivery_group' }
		}).done(function( data ) {
			jQuery(".all_group").html(data);
		});
		
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'get_delivery_area' }
		}).done(function( data ) {
			jQuery(".all_area").html(data);
		});
		
		jQuery('#add_group').click(function(){
			jQuery('.groupname').val('');
			jQuery('.groupareas').val(null).trigger('change');
			jQuery('.delete_group_form').removeClass('active');
			if (jQuery('.add_group').hasClass("active")) {
			  jQuery('.add_group').removeClass("active");
			}else{
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'add_group_area' }
				}).done(function( data ) {
					jQuery(".groupareas").html(data);
					jQuery('.groupareas').trigger('change');
				});
				jQuery('.add_group').addClass("active");
			}
			var target = jQuery('.add_group');
			if( target.length ) {
				event.preventDefault();
				jQuery('html, body').stop().animate({
					scrollTop: target.offset().top - 100
				}, 1000);
			}
		});
		
		jQuery('#create_group').click(function(){
			var groupName = jQuery('.groupname').val();
			var groupAreas = jQuery('.groupareas').val();
			var current = jQuery(this);
			var error = 0;
			if(!groupName){
				jQuery(".groupname").parent().find('span').css('display','block');
				setTimeout(function() { jQuery(".groupname").parent().find('span').hide('slow'); }, 3000);
				error = 1;
			}
			if(error==0){
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'create_delivery_group' , group_name : groupName, group_areas : groupAreas }
				}).done(function( data ) {
					jQuery('.add_group').removeClass("active");
					jQuery(".success_msg").html('Group Created Successfully');
					jQuery(".success_msg").css('display','inline-block');
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: { action: 'get_delivery_group' }
					}).done(function( data ) {
						jQuery(".all_group").html(data);
					});
					setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
				});
			}
		});
		
		jQuery('#create_area').click(function(){
			var areaname = jQuery('.areaname').val();
			var current = jQuery(this);
			var error = 0;
			if(!areaname){
				jQuery(".areaname").parent().find('span').css('display','block');
				setTimeout(function() { jQuery(".areaname").parent().find('span').hide('slow'); }, 3000);
				error = 1;
			}
			if(error==0){
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'create_delivery_area' , area_name : areaname }
				}).done(function( data ) {
					jQuery('.areaname').val('');
					jQuery('.add_group').removeClass("active");
					jQuery(".success_msg").html('Area Created Successfully');
					jQuery(".success_msg").css('display','inline-block');
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: { action: 'get_delivery_area' }
					}).done(function( data ) {
						jQuery(".all_area").html(data);
					});
					setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
				});
			}
		});
		
		jQuery('body').on('click', '.delete_group', function(event) {
			var groupID = jQuery(this).data('id');
			var result = confirm("Are you sure want to delete the group#"+groupID+" ?");
			if (result) {
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'delete_delivery_group' , group_id : groupID}
				}).done(function( data ) {
					jQuery(".success_msg").html('Group Deleted Successfully');
					jQuery(".success_msg").css('display','inline-block');
					setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: { action: 'get_delivery_group'}
					}).done(function( data ) {
						jQuery(".all_group").html(data);
					});
				});
			}
		});
		
		jQuery('body').on('click', '.delete_area', function(event) {
			var areaID = jQuery(this).data('id');
			var result = confirm("Are you sure want to delete the area#"+areaID+" ?");
			if (result) {
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'delete_delivery_area' , area_id : areaID}
				}).done(function( data ) {
					jQuery(".success_msg").html('Area Deleted Successfully');
					jQuery(".success_msg").css('display','inline-block');
					setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: { action: 'get_delivery_area'}
					}).done(function( data ) {
						jQuery(".all_area").html(data);
					});
				});
			}
		});
		
		jQuery('body').on('click', '.edit_group', function(event) {
			var groupID = jQuery(this).data('id');
			jQuery('.delete_group_form').removeClass('active');
			jQuery('.delete_group_form .titledesc span').html('#'+groupID);
			jQuery('.add_group').removeClass("active");
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'get_delivery_group_by_id' , group_id : groupID}
			}).done(function( data ) {
				data = data.split("#:");
				groupName = data[0];
				groupAreas = data[1];
				jQuery('.update_group_id').val(groupID);
				jQuery('.updategroupname').val(groupName); 
				jQuery('.updategroupareas').html(groupAreas); 
				jQuery('.updategroupareas').trigger('change');
				jQuery('.delete_group_form').addClass('active');
				var target = jQuery('.delete_group_form');
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
			});
		});
		
		jQuery('body').on('click', '.edit_area', function(event) {
			var areaID = jQuery(this).data('id');
			jQuery('.delete_group_form').removeClass('active');
			jQuery('.delete_group_form .titledesc span').html('#'+areaID);
			jQuery('.add_group').removeClass("active");
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'get_delivery_area_by_id' , area_id : areaID}
			}).done(function( data ) {
				areaName = data;
				jQuery('.update_area_id').val(areaID);
				jQuery('.updateareaname').val(areaName);
				jQuery('.delete_group_form').addClass('active');
				var target = jQuery('.delete_group_form');
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
			});
		});
		
		jQuery('.update_group_btn').click(function(){
			var groupName = jQuery('.updategroupname').val();
			var groupAreas = jQuery('.updategroupareas').val();
			var groupID = jQuery('.update_group_id').val();
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'update_delivery_group' , group_id : groupID ,group_name : groupName, group_areas : groupAreas}
			}).done(function( data ) {
				jQuery(".success_msg").html('Group Updated Successfully');
				jQuery(".success_msg").css('display','inline-block');
				var target = jQuery(".success_msg");
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
				jQuery('.delete_group_form').removeClass('active');
				jQuery('.update_group_id').val('');
				jQuery('.updategroupname').val('');
				jQuery('.updategroupareas').val('');
				setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'get_delivery_group'}
				}).done(function( data ) {
					jQuery(".all_group").html(data);
				});
			});
		})
		
		jQuery('.update_area_btn').click(function(){
			var areaName = jQuery('.updateareaname').val();
			var areaID = jQuery('.update_area_id').val();
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'update_delivery_area' , area_id : areaID ,area_name : areaName}
			}).done(function( data ) {
				jQuery(".success_msg").html('Area Updated Successfully');
				jQuery(".success_msg").css('display','inline-block');
				var target = jQuery(".success_msg");
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
				jQuery('.delete_group_form').removeClass('active');
				jQuery('.update_area_id').val('');
				jQuery('.updateareaname').val('');
				setTimeout(function() { jQuery(".success_msg").hide('slow'); }, 5000);
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'get_delivery_area'}
				}).done(function( data ) {
					jQuery(".all_area").html(data);
				});
			});
		})
		
		jQuery('body').on('click', '.custom_pagination a', function(event) {
			event.preventDefault();
			page = jQuery(this).data('page');
			current = jQuery(this).data('current');
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'get_delivery_area', page : page , current : current}
			}).done(function( data ) {
				jQuery(".all_area").html(data);
				var target = jQuery(".all_area");
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
			});
		});
		
		jQuery('.close_box').click(function(){
			jQuery('.add_group').removeClass("active");
			jQuery('.delete_group_form').removeClass('active');
		})
		
		if (jQuery('body').hasClass("post-php") && jQuery('body').hasClass("post-type-shop_order")) {
		var geocoder;
		var map;
		var address = jQuery(".verify_address").html();
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(-34.397, 150.644);
		var myOptions = {
		  zoom: 16,
		  center: latlng,
		mapTypeControl: true,
		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
		navigationControl: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		if (geocoder) {
			  geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
				  if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
					map.setCenter(results[0].geometry.location);
					var infowindow = new google.maps.InfoWindow(
						{ content: '<b>'+address+'</b>',
						  size: new google.maps.Size(150,50)
						});
			var marker = new google.maps.Marker({
				position: results[0].geometry.location,
				map: map, 
				title:address
			}); 
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});
			} else {
				alert("No results found");
			}
			} else {
				jQuery("#map_canvas").css('display','none');
				jQuery(".address_error").html('Address not shown for the following reason: "<b>' + status+'</b>"');
				jQuery(".address_error").css('display','block');
			}
			});
		}
		}
		
		var order_id = jQuery('.verify_address_order_id').html();
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'get_address_verify', order_id : order_id}
		}).done(function( data ) {
			jQuery('.proper_address').val(data);
		});
		
		jQuery('.updateaddress').click(function(e){
			e.preventDefault();
			var order_id = jQuery('.verify_address_order_id').html();
			var update_status = jQuery('.proper_address').val();
			if(update_status!=0){
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: { action: 'update_address_verify', order_id : order_id , update_status : update_status}
				}).done(function( data ) {
					alert('Status updated');
				});
			}else{
				alert('Please Select status');
			}
		});
	});
	</script>
<?php }
add_action('admin_footer', 'my_admin_delivery_groups_function');

function update_address_verify(){
	$order_id = $_REQUEST['order_id'];
	$update_status = $_REQUEST['update_status'];
	update_post_meta( $order_id, 'address_status', $update_status);
	exit;
}
add_action('wp_ajax_update_address_verify', 'update_address_verify');

function get_address_verify(){
	$order_id = $_REQUEST['order_id'];
	echo get_post_meta( $order_id, 'address_status' , true );
	exit;
}
add_action('wp_ajax_get_address_verify', 'get_address_verify');

add_action( 'admin_enqueue_scripts', 'remove_menu_pages_for_admin' ,2);
function remove_menu_pages_for_admin() {
	$user_ID = get_current_user_id();
	if($user_ID !=0 && $user_ID ==1 ){
		$user = get_userdata( $user_ID );
		$user_roles = $user->roles;
		if ( in_array( 'administrator', $user_roles, true ) ) {
			echo '<style>.menu-icon-plugins,.wp-submenu-wrap,.menu-icon-comments,.menu-icon-page,.menu-icon-notification,.toplevel_page_dots_store,.menu-icon-popup,.toplevel_page_wpcf7,.menu-icon-subscription,.toplevel_page_phoeniixx,.menu-icon-appearance,.menu-icon-tools,.menu-icon-settings,.toplevel_page_crellyslider,.toplevel_page_wpbizplugins_uac_options,#collapse-menu,.toplevel_page_wolfsettings,.menu-top.menu-icon-media,.menu-icon-kitchen{display:none}</style>';
		}
	}
	echo '<style>.menu-icon-kitchen{display:none}</style>';
}

add_action('wp_dashboard_setup', 'dashboard_report_widgets');
  
function dashboard_report_widgets() {
global $wp_meta_boxes;
$timezone = date("Y-m-d");
wp_add_dashboard_widget('custom_help_widget2', 'Weekly Stats', 'dashboard_report');
}
 
function dashboard_report() {
	global $wpdb;
	$results = $wpdb->get_results ( 'SELECT * FROM  wp_subscriptio_scheduled_events where event_key="order"');
	$first_day_of_the_week = 'Monday';
	$start_of_the_week     = strtotime("Last $first_day_of_the_week");
	if ( strtolower(date('l')) === strtolower($first_day_of_the_week) )
	{
		$start_of_the_week = strtotime('today');
	}
	$end_of_the_week = $start_of_the_week + (60 * 60 * 24 * 7) - 1;
	$date_format =  'Y-m-d h:i a';
	$currentstart = date($date_format, $start_of_the_week);
	$currentend = date($date_format, $end_of_the_week);
	$sub_array = array();
	foreach($results as $result){
		$s = new Subscriptio;
		$subscription = $s->load_from_cache('subscriptions', $result->subscription_id);
		if($subscription->status == 'active' && !in_array($result->subscription_id, $sub_array)){
			$sub_array[] = $result->subscription_id;
		}
	}
	$startcounter = 0;
	$endcounter = 0;
	foreach($sub_array as $sub){
		$s = new Subscriptio;
		$subscription = $s->load_from_cache('subscriptions', $sub);
		$startdatestt = $subscription->started;
		$startdate = date('Y-m-d h:i a', $startdatestt);
		$enddatestt = $subscription->expires;
		$enddate = date('Y-m-d h:i a', $enddatestt);
		if (($startdate > $currentstart) && ($startdate < $currentend)){
			$startcounter++;
		}
		if (($enddate > $currentstart) && ($enddate < $currentend)){
			$endcounter++;
		}
	}
	if($startcounter<10){
		$startcounter = '0'.$startcounter;
	}
	if($endcounter<10){
		$endcounter = '0'.$endcounter;
	}
	?>
	<style>.reportstat h3{font-weight:bold !important}</style>
		<div class="inside reportstat">
			<h3>New Customer Registered for this week - <span><?php echo $startcounter; ?></span></h3>
			<h3>Customer subscription finishing in this week - <span><?php echo $endcounter; ?></span></h3>
		</div>
<?php }

add_filter( 'manage_edit-shop_order_columns', 'additional_order_columns' );
function additional_order_columns( $columns ) {
	$new_columns = ( is_array( $columns ) ) ? $columns : array();
  	
  	$new_columns['customer_name'] = 'Customer Name';
  	$new_columns['customer_contact'] = 'Customer Contact';
  	$new_columns['customer_area'] = 'Customer Area';
	$new_columns['customer_notes'] = 'Customer Notes';
	
  	return $new_columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'additional_order_columns_value', 2 );
function additional_order_columns_value( $column ) {
	global $post;
	$order_id = $post->ID;
	$order_meta = get_post_meta($order_id);
	$order = wc_get_order($order_id);
	$notes = '';
	foreach ($order->get_items() as $item_id => $item) {
		$notes .= wc_get_order_item_meta($item_id , 'Add notes to Order');
	}
	if($notes == ""){
		$notes = "-";
	}
	if ( $column == 'customer_name' ) {
		echo ucfirst($order_meta['_billing_first_name'][0]).' '.ucfirst($order_meta['_billing_last_name'][0]);
	}
	
	if ( $column == 'customer_contact' ) {
		echo $order_meta['_billing_phone'][0];
	}
	
	if ( $column == 'customer_area' ) {
		echo get_post_meta($post->ID, 'billing_customer_area', true);
	}
	
	if ( $column == 'customer_notes' ) {
		echo $notes;
	}
}

add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) { 
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}

add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
function change_default_checkout_country() {
  return 'KW';
}

function woo_override_checkout_fields( $fields ) { 

	$fields['billing']['billing_country'] = array(
		'type'      => 'select',
		'label'     => __('Country', 'woocommerce'),
		'options' 	=> array('KW' => 'Kuwait')
	);

	return $fields; 
} 
add_filter( 'woocommerce_checkout_fields' , 'woo_override_checkout_fields' );

function auto_login_new_user( $user_id ) {
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    $user = get_user_by( 'id', $user_id );
    do_action( 'wp_login', $user->user_login );
    wp_redirect( home_url() );
    exit;
}
add_action( 'user_register', 'auto_login_new_user' );


/* Drivers Settings Page */
class drivers_Settings_Page {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wph_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_fields' ) );
	}
	public function wph_create_settings() {
		$page_title = 'Drivers';
		$menu_title = 'Drivers';
		$capability = 'manage_options';
		$slug = 'drivers';
		$callback = array($this, 'wph_settings_content');
		$icon = 'dashicons-admin-settings';
		$position = 9;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
	}
	public function wph_settings_content() { ?>
		<div class="wrap">
		<style>
		.custom_pagination_driver {display: inline-block;margin-top:20px}
		.custom_pagination_driver a {border: 1px solid #000;color: black;display: inline-block;padding: 8px 16px;text-decoration: none;margin: 0px 1px;}
		.custom_pagination_driver a.active {background-color: #000;color: #fff;}
		.custom_pagination_driver a:hover:not(.active) {background-color: #000;color:#fff}
		.center{text-align:center}
		</style>
		
			<?php
				if(isset($_GET['driver'])){
					$driver = $_GET['driver'];
				}else{
					$driver = '';
				}
				$area_per_page = 2;
				$foundarea = 0;
				$the_query = new WP_Query( array(
					'post_type'=> 'notification',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'orderby' => 'date',
					'order' => 'DESC',
				));
				while ($the_query -> have_posts()) : $the_query -> the_post();
						$metas = get_post_meta(get_the_ID(),'notification');
						$metas = array_reverse($metas);
						foreach($metas as $meta){
							$meta = explode(":#",$meta);
							$notetype = $meta[0];
							if($notetype == 'setdeliverydriver'){
								if($driver == '' or $driver == $meta[2] or $driver == 'all' ){
									$foundarea++;
								}
							}
						}
				endwhile;
				wp_reset_postdata();
				$total_pages = ceil($foundarea / $area_per_page);
				$page = 1;
				if($page == 1){
					$area_skipper = 0;
				}else{
					$area_skipper = ($page-1)*$area_per_page;
				}
				$args = array(
					'role'    => 'driver',
					'orderby' => 'user_nicename',
					'order'   => 'ASC'
				);
				$users = get_users( $args );
			?>
			<h1>Drivers</h1>
			<p><strong>Select Driver: </strong>
			<select class="driverselect">
			<option value="all">All</option>
			<?php foreach ( $users as $user ) { 
			if($user->data->ID == $driver){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			?>
				<option <?php echo $selected;?> value="<?php echo $user->data->ID; ?>"><?php echo $user->data->user_nicename; ?></option>
			<?php } ?>
			</select></p>
			<?php settings_errors(); 
			$dashboard = '<div class="driver-wrap"><div class="driver-dashboard"><table class="wp-list-table widefat fixed striped posts"><tr><th>Driver</th><th>Order#</th><th>Meal</th><th>Location</th><th>Assigned on</th><th>Status</th></tr>';
			$the_query = new WP_Query( array(
				'post_type'=> 'notification',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
			));
			$counter = 1;
			$breakwhileloop = $area_per_page;
			while ($the_query -> have_posts()) : $the_query -> the_post();
				$metas = get_post_meta(get_the_ID(),'notification');
				$metas = array_reverse($metas);
				$countercustom = 0;
				foreach($metas as $meta){
					$meta = explode(":#",$meta);
					$notetype = $meta[0];
					if($notetype == 'setdeliverydriver'){
						if($driver == '' or $driver == $meta[2] or $driver == 'all' ){
							if($counter <= $breakwhileloop){
								if($area_skipper==0){
									$mealready_order_id = explode(" ",get_the_title($meta[1]));
									$mealready_order_id = $mealready_order_id[1];
									$status_metas = get_post_meta(get_the_ID(),'notification');
									$status_flag=0;
									foreach($status_metas as $status_meta){
										$status_meta = explode(":#",$status_meta);
										$notetype = $status_meta[0];
										if($notetype == 'driverdeliveryupdate'){
											if($status_meta[1] == $meta[1] && $status_meta[5] == $meta[3]){
												$status_flag=1;
												$status = $status_meta[4];
												$status_note = $status_meta[3];
												$status_time = $status_meta[6];
											}
										}
									}
									$delivery_post = $meta[1];
									$order_id = get_the_title($meta[1]);
									$order_id = explode(" ",$order_id);
									$order_id = explode("#",$order_id[1]);
									$order_id = $order_id[1];
									$customer_id = get_customerorderid($order_id);
									if(get_the_author_meta( 'customer_flag', $customer_id ) == 'yes'){
										$flag = '<img src="'.get_site_url().'/wp-content/uploads/2018/09/flag-map-marker_318-50576.png"/><br>';
									}else{
										$flag = '';
									}
									$order_data = getOrderDetailById($order_id);
									$address = $order_data['order']['billing_address'];
									$user = get_user_by('id', $meta[2]);
									$dashboard .= '<tr><td><a href="'.get_site_url().'/wp-admin/user-edit.php?user_id='.$meta[2].'">'.ucfirst($user->data->user_nicename).'</td><td>'.$order_id.'</td><td>';
									foreach($order_data['order']['line_items'] as $meal){
										//$dashboard .= '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
										$dashboard .= '<p><strong>' . $meal['name'] . '<strong></p>';
									}
									$dashboard .= '</td><td><strong>' . $address['first_name'].' '.$address['last_name'] . '</strong><br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td><td>'.$flag.$meta[4].'</td><td>';
									if($status_flag == 0){
										$dashboard .= 'Pending';
										//$dashboard .='<a class="update_delivery_status" href="'.get_site_url().'/manage-delivery?delivery_id='.$meta[1].'">Manage</a></td></tr>';
									}else{
										$dashboard .= $status.' on '.$status_time;
										if($status_note !=''){
											$dashboard .= '<br>Notes:<br>'.$status_note.'</td></tr>';
										}
									}
									$counter++;
								}else{
									$area_skipper--;
								}
							}
						}
					}
				}
			endwhile;
			wp_reset_postdata();
			$dashboard .= '</table>';
			$dashboard .= '<div class="center"><div class="custom_pagination_driver"><a data-driver="'.$driver.'" data-current="'.$page.'" data-page="prev" href="#">&laquo;</a>';
			for($i=1;$i<=$total_pages;$i++){
				if($page == $i){
					$dashboard .= '<a data-current="'.$page.'" href="#" class="active" data-driver="'.$driver.'" data-page="'.$i.'">'.$i.'</a>';
				}else{
					$dashboard .= '<a data-current="'.$page.'" href="#" data-driver="'.$driver.'" data-page="'.$i.'">'.$i.'</a>';
				}
			}
			$dashboard .= '<a data-current="'.$page.'" data-page="next" data-driver="'.$driver.'" href="#">&raquo;</a></div></div></div></div>';
			echo $dashboard;
	}
	public function wph_setup_sections() {
		add_settings_section( 'drivers_section', 'All drivers', array(), 'drivers' );
	}
	public function wph_setup_fields() {
		$fields = array(
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'wph_field_callback' ), 'drivers', $field['section'], $field );
			register_setting( 'drivers', $field['id'] );
		}
	}
	public function wph_field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}
}
new drivers_Settings_Page();

function my_admin_driver_script() { ?>
	<script>
	jQuery(document).ready(function() {
		jQuery('.driverselect').change(function(){
			window.location.href = '<?php echo get_site_url()?>'+'/wp-admin/admin.php?page=drivers&driver='+jQuery('.driverselect').val()
		});
		
		jQuery('body').on('click', '.custom_pagination_driver a', function(event) {
			event.preventDefault();
			page = jQuery(this).data('page');
			current = jQuery(this).data('current');
			driver = jQuery(this).data('driver');
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'get_driver_delivery', page : page , current : current,driver:driver}
			}).done(function( data ) {
				console.log(data);
				jQuery(".driver-wrap").html(data);
				var target = jQuery(".driver-wrap");
				if( target.length ) {
					event.preventDefault();
					jQuery('html, body').stop().animate({
						scrollTop: target.offset().top - 100
					}, 1000);
				}
			});
		});
		
	});
	</script>
<?php }
add_action('admin_footer', 'my_admin_driver_script');

function get_driver_delivery(){
	global $wpdb;
	if(isset($_REQUEST['driver'])){
		$driver = $_REQUEST['driver'];
	}else{
		$driver = '';
	}
	$area_per_page = 2;
	$foundarea = 0;
	$the_query = new WP_Query( array(
		'post_type'=> 'notification',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	while ($the_query -> have_posts()) : $the_query -> the_post();
		$metas = get_post_meta(get_the_ID(),'notification');
		$metas = array_reverse($metas);
		foreach($metas as $meta){
			$meta = explode(":#",$meta);
			$notetype = $meta[0];
			if($notetype == 'setdeliverydriver'){
				if($driver == '' or $driver == $meta[2] or $driver == 'all' ){
					$foundarea++;
				}
			}
		}
	endwhile;
	wp_reset_postdata();
	$total_pages = ceil($foundarea / $area_per_page);
	if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'prev'){
		if($_REQUEST['current'] == 1){
			$page = 1;
		}else{
			$page = $_REQUEST['current'] - 1;
		}
	}else if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'next'){
		if($_REQUEST['current'] == $total_pages){
			$page = $total_pages;
		}else{
			$page = $_REQUEST['current'] + 1;
		}
	}else if(isset($_REQUEST['page']) and ($_REQUEST['page'] != 'prev' or $_REQUEST['page'] != 'next')){
		$page = $_REQUEST['page'];
	}else{
		$page = 1;
	}
	if($page == 1){
		$area_skipper = 0;
	}else{
		$area_skipper = ($page-1)*$area_per_page;
	}
	$dashboard = '<div class="driver-dashboard"><table class="wp-list-table widefat fixed striped posts"><tr><th>Driver</th><th>Order#</th><th>Meal</th><th>Location</th><th>Assigned on</th><th>Status</th></tr>';
			$the_query = new WP_Query( array(
				'post_type'=> 'notification',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
			));
			$counter = 1;
			$breakwhileloop = $area_per_page;
			while ($the_query -> have_posts()) : $the_query -> the_post();
				$metas = get_post_meta(get_the_ID(),'notification');
				$metas = array_reverse($metas);
				$countercustom = 0;
				foreach($metas as $meta){
					$meta = explode(":#",$meta);
					$notetype = $meta[0];
					if($notetype == 'setdeliverydriver'){
						if($driver == '' or $driver == $meta[2] or $driver == 'all' ){
							if($counter <= $breakwhileloop){
								if($area_skipper==0){
									$mealready_order_id = explode(" ",get_the_title($meta[1]));
									$mealready_order_id = $mealready_order_id[1];
									$status_metas = get_post_meta(get_the_ID(),'notification');
									$status_flag=0;
									foreach($status_metas as $status_meta){
										$status_meta = explode(":#",$status_meta);
										$notetype = $status_meta[0];
										if($notetype == 'driverdeliveryupdate'){
											if($status_meta[1] == $meta[1] && $status_meta[5] == $meta[3]){
												$status_flag=1;
												$status = $status_meta[4];
												$status_note = $status_meta[3];
												$status_time = $status_meta[6];
											}
										}
									}
									$delivery_post = $meta[1];
									$order_id = get_the_title($meta[1]);
									$order_id = explode(" ",$order_id);
									$order_id = explode("#",$order_id[1]);
									$order_id = $order_id[1];
									$customer_id = get_customerorderid($order_id);
									if(get_the_author_meta( 'customer_flag', $customer_id ) == 'yes'){
										$flag = '<img src="'.get_site_url().'/wp-content/uploads/2018/09/flag-map-marker_318-50576.png"/><br>';
									}else{
										$flag = '';
									}
									$order_data = getOrderDetailById($order_id);
									$address = $order_data['order']['billing_address'];
									$user = get_user_by('id', $meta[2]);
									$dashboard .= '<tr><td><a href="'.get_site_url().'/wp-admin/user-edit.php?user_id='.$meta[2].'">'.ucfirst($user->data->user_nicename).'</td><td>'.$order_id.'</td><td>';
									foreach($order_data['order']['line_items'] as $meal){
										//$dashboard .= '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
										$dashboard .= '<p><strong>' . $meal['name'] . '<strong></p>';
									}
									$dashboard .= '</td><td><strong>' . $address['first_name'].' '.$address['last_name'] . '</strong><br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td><td>'.$flag.$meta[4].'</td><td>';
									if($status_flag == 0){
										$dashboard .= 'Pending';
										//$dashboard .='<a class="update_delivery_status" href="'.get_site_url().'/manage-delivery?delivery_id='.$meta[1].'">Manage</a></td></tr>';
									}else{
										$dashboard .= $status.' on '.$status_time;
										if($status_note !=''){
											$dashboard .= '<br>Notes:<br>'.$status_note.'</td></tr>';
										}
									}
									$counter++;
								}else{
									$area_skipper--;
								}
							}
						}
					}
				}
			endwhile;
			wp_reset_postdata();
			$dashboard .= '</table></div></div>';
			$dashboard .= '<div class="center"><div class="custom_pagination_driver"><a data-driver="'.$driver.'" data-current="'.$page.'" data-page="prev" href="#">&laquo;</a>';
			for($i=1;$i<=$total_pages;$i++){
				if($page == $i){
					$dashboard .= '<a data-current="'.$page.'" href="#" data-driver="'.$driver.'" class="active" data-page="'.$i.'">'.$i.'</a>';
				}else{
					$dashboard .= '<a data-current="'.$page.'" href="#" data-driver="'.$driver.'" data-page="'.$i.'">'.$i.'</a>';
				}
			}
			$dashboard .= '<a data-current="'.$page.'" data-page="next" data-driver="'.$driver.'" href="#">&raquo;</a></div></div>';
			echo $dashboard;
	exit;
}
add_action('wp_ajax_get_driver_delivery', 'get_driver_delivery');

function generate_driver_report(){
 if(isset($_GET['export_excel2'])){
	 echo 'gotcha';
			/*require_once dirname(__FILE__). '/Classes/PHPExcel.php';
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("Wolf Nutrition")
										 ->setLastModifiedBy("Wolf Nutrition")
										 ->setTitle("Driver Report")
										 ->setSubject("Driver Report")
										 ->setDescription("Document for driver report.")
										 ->setKeywords("Report")
										 ->setCategory("Report");


			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'Driver')
						->setCellValue('B1', 'Order#')
						->setCellValue('C1', 'Meal')
						->setCellValue('D1', 'Location')
						->setCellValue('E1', 'Assigned On')
						->setCellValue('F1', 'Status');
						
			for($i=2;$i<12;$i++){
				$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$i, rand(1,10))
						->setCellValue('B'.$i, rand(10,20))
						->setCellValue('C'.$i, rand(20,30))
						->setCellValue('D'.$i, rand(30,40))
						->setCellValue('E'.$i, rand(40,50))
						->setCellValue('F'.$i, rand(50,60));
			}

			$objPHPExcel->getActiveSheet()->setTitle('Report 01-11-2018');

			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="01simple.xls"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Cache-Control: cache, must-revalidate');
			header('Pragma: public');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			wp_redirect('http://13.127.28.3/~ankur/click4demos_in/projects/fooddelivery/wp-admin/admin.php?page=drivers&driver=all');*/
		}
}
add_action('init','generate_driver_report');
