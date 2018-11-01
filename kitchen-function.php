<?php 
class WPDocs_Kitchen_Meta_Box {

    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }

    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }
 
    public function add_metabox() {
        add_meta_box(
            'my-meta-box',
            __( 'Orders', 'textdomain' ),
            array( $this, 'render_metabox' ),
            'kitchen',
            'advanced',
            'default'
        );
 
    }

    public function render_metabox( $post ) {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		?>
		<style>
		#edit-slug-box,.wc-item-meta{display:none}
		</style>
		<table class="wp-list-table widefat fixed striped posts">
		<tr>
			<td>Order#</td>
			<td>Meal</td>
			<td>Delivery Location</td>
			<td>Current Status</td>
			<?php /*
			$user = wp_get_current_user();
			if ( in_array( 'administrator', (array) $user->roles ) ) { ?>
				<td>Meal Delivery</td>
			<?php }*/	?>
		</tr>
		<?php
		global $post;
		$kitchen_post = get_the_ID();
		$orders = explode(",",get_post_meta( $kitchen_post, 'today_orders' , true ));
		$user = wp_get_current_user();
		if ( in_array( 'administrator', (array) $user->roles ) ) {
			$flag = 1;
		}else{
			$flag = 0;
		}
		foreach($orders as $order){
			$order_id = $order;
			$order_data = getOrderDetailById($order_id);
			$address = $order_data['order']['billing_address'];
			$unique_meal_id = 'meal_id-'.$kitchen_post.'-'.$order_id;
			$meal_status = get_post_meta( $kitchen_post, $unique_meal_id , true );
			$delivery_meta_key = 'sent_delivery_'.$unique_meal_id;
			$delivery_status = get_post_meta( $kitchen_post, $delivery_meta_key , true );
			$hold_content = '<tr><td>' . $order_id . '</td><td>';
			$view_row = 0;
			foreach($order_data['order']['line_items'] as $meal){
				//echo '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
				$product_status = get_subscription_status($meal['product_id'],$order_id);
				if($product_status == 1){
					$hold_content .= '<p><strong>' . $meal['name'] .'<strong></p>';
					$view_row++;
				}
			}
			$hold_content .= '</td><td><strong>' . $address['first_name'].' '.$address['last_name'] . '</strong><br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td>';
			if($meal_status!=1){
				$hold_content .= '<td><p class="orderstatus">In Kitchen</p><input type="hidden" class="mealready" name="'.$unique_meal_id.'" value="' . esc_textarea( $meal_status )  . '"><a data-meal="'.$unique_meal_id.'" data-post="'.$kitchen_post.'" class="button save_order setmealready button-primary" name="save" value="Update">Set Meal Ready</a></td>';
			}else{
				$hold_content .= '<td><p class="orderstatus">Meal Ready</p><input type="hidden" class="mealready" name="'.$unique_meal_id.'" value="' . esc_textarea( $meal_status )  . '"></td>';
			}
			/*if($flag==1){
				if($meal_status!=1){
					$hold_content .= '<td><a disabled data-meal="'.$unique_meal_id.'" class="button save_order createmealdelivery button-primary">Create Meal Delivery</a></td>';
				}else{
					if($delivery_status!=1){
						$hold_content .= '<td><a data-meal="'.$unique_meal_id.'" class="button save_order createmealdelivery button-primary">Create Meal Delivery</a></td>';
					}else{
						$hold_content .= '<td>Delivery Created</td>';
					}
				}
			}*/
			$hold_content .= '</tr>';
			if($view_row != 0){
				echo $hold_content;
			}
		}
		?>
		</table>
		<?php
    }

    public function save_metabox( $post_id, $post ) {
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';

        if ( ! isset( $nonce_name ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
    }
}
 
new WPDocs_Kitchen_Meta_Box();

function kitchen_meal_update(){
    $meal_id = $_POST['meal_id'];
	$post_id = $_POST['post_id'];
	if ( get_post_meta( $post_id, $meal_id, false ) ) {
		update_post_meta( $post_id, $meal_id, '1' );
	} else {
		add_post_meta( $post_id, $meal_id, '1');
	}
	$timezone = date("Y-m-d h:m");
	add_post_meta( get_option( 'today_notification' ), 'notification', 'mealready:#'.$meal_id.':#'.$timezone, false );
	$unique_meal_id = $_POST['meal_id'];
	$meta_key = 'sent_delivery_'.$unique_meal_id;
	$unique_meal_id = explode('-',$unique_meal_id);
	$post_id = $unique_meal_id[1];
	$meal_id = $unique_meal_id[2];
	if ( get_post_meta( $post_id, $meta_key, false ) ) {
		update_post_meta( $post_id, $meta_key, '1' );
	} else {
		add_post_meta( $post_id, $meta_key, '1');
	}
	$timezone = date("Y-m-d h:m");
	$title = 'Order #'.$meal_id.' '.$timezone;
	$post_type = 'delivery';

	$new_post = array(
	'post_title'    => $title,
	'post_status'   => 'publish',
	'post_type'     => $post_type 
	);
	
	$pid = wp_insert_post($new_post);
	$meta_key = $pid.'order_id';
	add_post_meta($pid, $meta_key, $meal_id, true);
	add_post_meta($pid, 'meal_id', $_POST['meal_id'], true);
	$timezone = date("Y-m-d h:m");
	add_post_meta( get_option( 'today_notification' ), 'notification', 'createdelivery:#'.$_POST['meal_id'].':#'.$timezone, false );
	$returnurl = get_site_url().'/wp-admin/post.php?post='.$pid.'&action=edit';
	echo $returnurl;
    exit();
}
add_action('wp_ajax_kitchen_meal_update', 'kitchen_meal_update');

function create_meal_delivery(){
    $unique_meal_id = $_POST['meal_id'];
	$meta_key = 'sent_delivery_'.$unique_meal_id;
	$unique_meal_id = explode('-',$unique_meal_id);
	$post_id = $unique_meal_id[1];
	$meal_id = $unique_meal_id[2];
	if ( get_post_meta( $post_id, $meta_key, false ) ) {
		update_post_meta( $post_id, $meta_key, '1' );
	} else {
		add_post_meta( $post_id, $meta_key, '1');
	}
	$timezone = date("Y-m-d h:m");
	$title = 'Order #'.$meal_id.' '.$timezone;
	$post_type = 'delivery';

	$new_post = array(
	'post_title'    => $title,
	'post_status'   => 'publish',
	'post_type'     => $post_type 
	);
	
	$pid = wp_insert_post($new_post);
	$meta_key = $pid.'order_id';
	add_post_meta($pid, $meta_key, $meal_id, true);
	add_post_meta($pid, 'meal_id', $_POST['meal_id'], true);
	$timezone = date("Y-m-d h:m");
	add_post_meta( get_option( 'today_notification' ), 'notification', 'createdelivery:#'.$_POST['meal_id'].':#'.$timezone, false );
	$returnurl = get_site_url().'/wp-admin/post.php?post='.$pid.'&action=edit';
	echo $returnurl;
    exit();
}
add_action('wp_ajax_create_meal_delivery', 'create_meal_delivery');

function get_subscription_status($product_id, $order_id){
	global $wpdb;
	$the_query = new WP_Query( array(
		'post_type'=> 'subscription',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	while ($the_query -> have_posts()) : $the_query -> the_post();
		$s = new Subscriptio;
		$subscription = $s->load_from_cache('subscriptions', get_the_ID());
		if($subscription->last_order_id == $order_id && $subscription->product_id == $product_id){
			if($subscription->status == 'paused'){
				$flag = 0;
				break;
			}else{
				$started = date('m/d/Y h:i a', $subscription->started);
				$today = date('m/d/Y 24:00');
				if($started <= $today){
					$flag = 1;
				}else{
					$flag = 0;
				}
				break;
			}
		}
	endwhile;
	wp_reset_postdata();
	return $flag;
}

class kitchen_Settings_Page {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wph_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_fields' ) );
	}
	public function wph_create_settings() {
		$page_title = 'Kitchen';
		$menu_title = 'Kitchen';
		$capability = 'manage_options';
		$slug = 'kitchen';
		$callback = array($this, 'wph_settings_content');
		$icon = 'dashicons-admin-post';
		$position = 6;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
	}
	public function wph_settings_content() { ?>
		<div class="wrap">
			<h1>Kitchen</h1>
			<p><b><a href="javascript:void(0)" class="trigger_datepicker">View order by date</a></b></p>
			<?php
				if(isset($_GET['order_date'])){
					echo '<p><input type="text" class="datepicker-kitchen" value="'.$_GET['order_date'].'" /></p>';
					echo '<a data-date="'.$_GET['order_date'].'" data-meal="" class="button save_order bulksetmealready button-primary">Bulk Set Meal Ready</a><br><br>';
				}else{
					echo '<p><input type="text" class="datepicker-kitchen" /></p>';
					echo '<a data-date="" data-meal="" class="button save_order bulksetmealready button-primary">Bulk Set Meal Ready</a><br><br>';
				}
			?>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'kitchen' );
					do_settings_sections( 'kitchen' );
					//submit_button();
				?>
			</form>
			<style>
		#edit-slug-box,.wc-item-meta{display:none}
		</style>
		<table class="wp-list-table widefat fixed striped posts">
		<tr>
			<td style="width:50px"><input type="checkbox" id="all_meal_select"/></td>
			<td>Order#</td>
			<td>Customer Name</td>
			<td>Meal</td>
			<td>Delivery Location</td>
			<td>Area</td>
			<td>Current Status</td>
			<?php /*
			$user = wp_get_current_user();
			if ( in_array( 'administrator', (array) $user->roles ) ) { ?>
				<td>Meal Delivery</td>
			<?php }*/	?>
		</tr>
		<?php
		global $post;
		if(isset($_GET['order_date'])){
			$post_title = 'Today orders - '.$_GET['order_date'];
			$kitchen_post = get_page_by_title( $post_title,OBJECT, 'kitchen');
			$kitchen_post = $kitchen_post->ID;
			if(!$kitchen_post){
				$kitchen_post = 0;
			}
		}else{
			$post_title = 'Today orders - '.date("Y-m-d");
			$kitchen_post = get_page_by_title( $post_title,OBJECT, 'kitchen');
			$kitchen_post = $kitchen_post->ID;
		}
		if($kitchen_post !=0){
		$orders = explode(",",get_post_meta( $kitchen_post, 'today_orders' , true ));
		$user = wp_get_current_user();
		if ( in_array( 'administrator', (array) $user->roles ) ) {
			$flag = 1;
		}else{
			$flag = 0;
		}
		foreach($orders as $order){
			$order_id = $order;
			$temp_order_meta = get_post_meta($order_id);
			$customer_name = ucfirst($temp_order_meta['_billing_first_name'][0]).' '.ucfirst($temp_order_meta['_billing_last_name'][0]);
			$customer_area = get_post_meta($order_id, 'billing_customer_area', true);
			$order_data = getOrderDetailById($order_id);
			$address = $order_data['order']['billing_address'];
			$unique_meal_id = 'meal_id-'.$kitchen_post.'-'.$order_id;
			$meal_status = get_post_meta( $kitchen_post, $unique_meal_id , true );
			$delivery_meta_key = 'sent_delivery_'.$unique_meal_id;
			$delivery_status = get_post_meta( $kitchen_post, $delivery_meta_key , true );
			if($meal_status!=1){
				$stat = '';
			}else{
				$stat = 'disabled';
			}
			$hold_content = '<tr>';
			if($meal_status!=1){
				$hold_content .= '<td><input type="checkbox" name="meals[]" class="meal_checkbox" data-meal="'.$unique_meal_id.'" data-post="'.$kitchen_post.'"/></td>';
			}else{
				$hold_content .= '<td>-</td>';
			}
			$hold_content .= '<td>' . $order_id . '</td><td>'.$customer_name.'</td><td>';
			$view_row = 0;
			foreach($order_data['order']['line_items'] as $meal){
				//echo '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
				$product_status = get_subscription_status($meal['product_id'],$order_id);
				if($product_status == 1){
					$hold_content .= '<p><strong>' . $meal['name'] .'<strong></p>';
					$view_row++;
				}
			}
			$hold_content .= '</td><td>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td><td>'.$customer_area
			.'</td>';
			if($meal_status!=1){
				$hold_content .= '<td><p class="orderstatus">In Kitchen</p><input type="hidden" class="mealready" name="'.$unique_meal_id.'" value="' . esc_textarea( $meal_status )  . '"><a data-meal="'.$unique_meal_id.'" data-post="'.$kitchen_post.'" class="button save_order setmealready button-primary" name="save" value="Update">Set Meal Ready</a></td>';
			}else{
				$hold_content .= '<td><p class="orderstatus">Meal Ready</p><input type="hidden" class="mealready" name="'.$unique_meal_id.'" value="' . esc_textarea( $meal_status )  . '"></td>';
			}
			/*if($flag==1){
				if($meal_status!=1){
					$hold_content .= '<td><a disabled data-meal="'.$unique_meal_id.'" class="button save_order createmealdelivery button-primary">Create Meal Delivery</a></td>';
				}else{
					if($delivery_status!=1){
						$hold_content .= '<td><a data-meal="'.$unique_meal_id.'" class="button save_order createmealdelivery button-primary">Create Meal Delivery</a></td>';
					}else{
						$hold_content .= '<td>Delivery Created</td>';
					}
				}
			}*/
			$hold_content .= '</tr>';
			if($view_row != 0){
				echo $hold_content;
			}
		}
	}
		?>
		</table>
		</div> <?php
	}
	public function wph_setup_sections() {
		
	}
	public function wph_setup_fields() {
		$fields = array(
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'wph_field_callback' ), 'kitchen', $field['section'], $field );
			register_setting( 'kitchen', $field['id'] );
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
new kitchen_Settings_Page();

function my_admin_kitchen_script() { ?>
	<script>
	jQuery(document).ready(function() {
		jQuery('.datepicker-kitchen').datepicker({ maxDate: 0,dateFormat: 'yy-mm-dd'})
		jQuery(".trigger_datepicker").click(function(){
			jQuery('.datepicker-kitchen').datepicker("show");
		});
		jQuery('.datepicker-kitchen').change(function(){
			window.location.href = '<?php echo get_site_url()?>'+'/wp-admin/admin.php?page=kitchen&order_date='+jQuery('.datepicker-kitchen').val()
		});
	});
	</script>
<?php }
add_action('admin_footer', 'my_admin_kitchen_script');

function bulk_kitchen_meal_update(){
    $meal_id = $_POST['meal_id'];
	$post_id = $_POST['post_id'];
	$date = $_POST['date_selected'];
	$meal_ids = rtrim($meal_id,',');
	$meal_ids = explode(",",$meal_ids);
	foreach($meal_ids as $meal_id){
		if ( get_post_meta( $post_id, $meal_id, false ) ) {
			update_post_meta( $post_id, $meal_id, '1' );
		} else {
			add_post_meta( $post_id, $meal_id, '1');
		}
		$timezone = date("Y-m-d h:m");
		add_post_meta( get_option( 'today_notification' ), 'notification', 'mealready:#'.$meal_id.':#'.$timezone, false );
		$unique_meal_id = $meal_id;
		$meta_key = 'sent_delivery_'.$unique_meal_id;
		$unique_meal_id = explode('-',$unique_meal_id);
		$post_id = $unique_meal_id[1];
		$meal_id = $unique_meal_id[2];
		if ( get_post_meta( $post_id, $meta_key, false ) ) {
			update_post_meta( $post_id, $meta_key, '1' );
		} else {
			add_post_meta( $post_id, $meta_key, '1');
		}
		$timezone = date("Y-m-d h:m");
		$title = 'Order #'.$meal_id.' '.$timezone;
		$post_type = 'delivery';

		$new_post = array(
		'post_title'    => $title,
		'post_status'   => 'publish',
		'post_type'     => $post_type 
		);
		
		$pid = wp_insert_post($new_post);
		$meta_key = $pid.'order_id';
		add_post_meta($pid, $meta_key, $meal_id, true);
		add_post_meta($pid, 'meal_id', $_POST['meal_id'], true);
		$timezone = date("Y-m-d h:m");
		add_post_meta( get_option( 'today_notification' ), 'notification', 'createdelivery:#'.$_POST['meal_id'].':#'.$timezone, false );
	}
	if($date!=""){
		$returnurl = get_site_url().'/wp-admin/admin.php?page=kitchen&order_date='.$date;
	}else{
		$returnurl = get_site_url().'/wp-admin/admin.php?page=kitchen';
	}
	echo $returnurl;
    exit();
}
add_action('wp_ajax_bulk_kitchen_meal_update', 'bulk_kitchen_meal_update');
