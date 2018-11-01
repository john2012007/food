<?php 
class WPDocs_Delivery_Meta_Box {

    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }

    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_delivery'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox_delivery' ), 10, 2 );
    }

    public function add_metabox_delivery() {
        add_meta_box(
            'my-meta-box',
            __( 'Orders', 'textdomain' ),
            array( $this, 'render_metabox_delivery' ),
            'delivery',
            'advanced',
            'default'
        );
 
    }
 
    public function render_metabox_delivery( $post ) {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		?>
		<style>
		#edit-slug-box{display:none}
		</style>
		<table class="wp-list-table widefat fixed striped posts">
		<tr>
			<td>Order#</td>
			<td>Meal</td>
			<td>Delivery Location</td>
			<td>Area</td>
			<td>Select Driver</td>
			<td></td>
		</tr>
		<?php
		global $wpdb;
		$delivery_post = get_the_ID();
		//delete_post_meta( $delivery_post, 'selected_driver');
		$order_id = get_the_title();
		$order_id = explode(" ",$order_id);
		$order_id = explode("#",$order_id[1]);
		$order_id = $order_id[1];
		$order_data = getOrderDetailById($order_id);
		$address = $order_data['order']['billing_address'];
		$unique_meal_id = get_post_meta( $delivery_post, 'meal_id' , true );
		$delivery_meta_key = 'sent_delivery_'.$unique_meal_id;
		$delivery_status = get_post_meta( $delivery_post, $delivery_meta_key , true );
		$driver_id = get_post_meta( $delivery_post, 'selected_driver' , true );
		$area = get_post_meta( $order_id, 'billing_customer_area' , true );
		//update_user_meta( 5, 'driver_availability', 'Free');
		$args = array(
			'role'    => 'driver',
			'orderby' => 'user_nicename',
			'order'   => 'ASC'
		);
		$users = get_users( $args );
		if($unique_meal_id){
			echo '<tr><td>' . $order_id . '</td><td>';
			foreach($order_data['order']['line_items'] as $meal){
				//echo '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
				//$product_status = get_subscription_status($meal['product_id'],$order_id);
				//if($product_status == 1){
					echo '<p><strong>' . $meal['name'] .'<strong></p>';
				//}
			}
			echo '</td><td><strong>' . $address['first_name'].' '.$address['last_name'] . '</strong><br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td><td>'.$area.'</td>';
			
			if($driver_id){
				echo '<td><strong class="drivername">';
				foreach ( $users as $user ) {
					if($driver_id == $user->ID){
						echo esc_html( ucfirst($user->display_name) );
					}
				}
				echo '</strong></td>';
				echo '<td><a disabled data-delivery="'.$delivery_post.'" class="button save_order setdeliverydriver button-primary">Set Delivery Driver</a></td>';
			}else{
				echo '<td>Available '.$area.' Drivers:<br><select class="driverselect"><option value="0">Select Drivers</option>';
				foreach ( $users as $user ) {
					if(get_user_meta( $user->ID, 'driver_availability', true ) == 'Free'){
						$groups = get_the_author_meta( 'driver_area', $user->ID );
						$groups = explode(",",$groups);
						foreach($groups as $group){
							$results = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_groups where group_id="'.$group.'"');
							$areas = explode(", ",$results[0]->area_id);
							if($areas){
								foreach($areas as $a){
									$a = $wpdb->get_results ( 'SELECT * FROM  wp_delivery_areas where id="'.$a.'"');
									if($a[0]->area_name == $area){
										echo '<option value="'.esc_html( $user->ID ).'">' . esc_html( ucfirst($user->display_name) ).'</option>';
									}
								}
							}
						}
					}
				}
				echo '</select><strong class="drivername"></strong></td>';
				echo '<td><a data-delivery="'.$delivery_post.'" class="button save_order setdeliverydriver button-primary">Set Delivery Driver</a></td>';
			}
		}
		echo '</tr>';
		?>
		</table>
		<?php
    }
 
    public function save_metabox_delivery( $post_id, $post ) {
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
 
new WPDocs_Delivery_Meta_Box();

function set_delivery_driver(){
    $post_id = $_POST['delivery_id'];
	$driver_id = $_POST['driver_id'];
	$meta_key = 'delivery_created'.$post_id;
	if ( get_post_meta( $post_id, $meta_key, false ) ) {
		update_post_meta( $post_id, $meta_key, '1' );
	} else {
		add_post_meta( $post_id, $meta_key, '1');
	}
	if ( get_post_meta( $post_id, 'selected_driver', false ) ) {
		update_post_meta( $post_id, 'selected_driver', $driver_id );
	} else {
		add_post_meta( $post_id, 'selected_driver', $driver_id);
	}
	$driver = get_userdata($driver_id);
	$attempt = 1;
	$the_query = new WP_Query( array(
		'post_type'=> 'notification',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	while ($the_query -> have_posts()) : $the_query -> the_post();
		$metas = get_post_meta(get_the_ID(),'notification');
		foreach($metas as $meta){
			$meta = explode(":#",$meta);
			$notetype = $meta[0];
			if($notetype == 'setdeliverydriver'){
				if($meta[1]==$post_id){
					$attempt++;
				}
			}
		}
	endwhile;
	wp_reset_postdata();
	
	$timezone = date("Y-m-d h:m");
	add_post_meta( get_option( 'today_notification' ), 'notification', 'setdeliverydriver:#'.$post_id.':#'.$driver_id.':#'.$attempt.':#'.$timezone, false );
	update_user_meta( $driver_id, 'driver_availability', 'In transit');
	echo ucfirst($driver->display_name);
	exit();
}
add_action('wp_ajax_set_delivery_driver', 'set_delivery_driver');

add_filter( 'manage_delivery_posts_columns', 'set_custom_edit_delivery_columns' );
function set_custom_edit_delivery_columns($columns) {
    $columns['customer_name'] = __( 'Customer', 'your_text_domain' );
    $columns['meal'] = __( 'Meal', 'your_text_domain' );
    $columns['area'] = __( 'Area', 'your_text_domain' );
	$columns['driver'] = __( 'Driver', 'your_text_domain' );

    return $columns;
}

add_action( 'manage_delivery_posts_custom_column' , 'custom_delivery_column', 10, 2 );
function custom_delivery_column( $column, $post_id ) {
	$delivery_post = $post_id;
	$order_id = get_the_title($delivery_post);
	$order_id = explode(" ",$order_id);
	$order_id = explode("#",$order_id[1]);
	$order_id = $order_id[1];
	$order_data = getOrderDetailById($order_id);
	$unique_meal_id = get_post_meta( $delivery_post, 'meal_id' , true );
	$delivery_meta_key = 'sent_delivery_'.$unique_meal_id;
	$delivery_status = get_post_meta( $delivery_post, $delivery_meta_key , true );
	$driver_id = get_post_meta( $delivery_post, 'selected_driver' , true );
	$area = get_post_meta( $order_id, 'billing_customer_area' , true );
	$order_meta = get_post_meta($order_id);
	$args = array(
		'role'    => 'driver',
		'orderby' => 'user_nicename',
		'order'   => 'ASC'
	);
	$users = get_users( $args );
		
    switch ( $column ) {

		case 'customer_name' :
            echo ucfirst($order_meta['_billing_first_name'][0]).' '.ucfirst($order_meta['_billing_last_name'][0]);
            break;
        
		case 'meal' :
            foreach($order_data['order']['line_items'] as $meal){
				echo '<p><strong>' . $meal['name'] .'<strong></p>';
			}
            break;

        case 'area' :
            echo $area;
            break;
		
		case 'driver' :
            if($driver_id){
				echo '<strong class="drivername">';
				foreach ( $users as $user ) {
					if($driver_id == $user->ID){
						echo esc_html( ucfirst($user->display_name) );
					}
				}
				echo '</strong>';
				
			}else{
				echo 'Not Selected';
			}
            break;
    }
}
