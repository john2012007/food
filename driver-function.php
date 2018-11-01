<?php 
function driver_dashboard(){
	$dashboard = '<div class="driver-dashboard"><table><tr><th>Order#</th><th>Meal</th><th>Location</th><th>Assigned on</th><th>Status</th></tr>';
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
				$user = wp_get_current_user();
				if($user->data->ID==$meta[2]){
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
					$dashboard .= '<tr><td>'.$order_id.'</td><td>';
					foreach($order_data['order']['line_items'] as $meal){
						$dashboard .= '<p><strong>' . $meal['name'] . ' x '.$meal['quantity'].'<strong></p>';
					}
					$dashboard .= '</td><td><strong>' . $address['first_name'].' '.$address['last_name'] . '</strong><br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'</td><td>'.$flag.$meta[4].'</td><td>';
					if($status_flag == 0){
						$dashboard .='<a class="update_delivery_status" href="'.get_site_url().'/manage-delivery?delivery_id='.$meta[1].'">Manage</a></td></tr>';
					}else{
						$dashboard .= $status.' on '.$status_time;
						if($status_note !=''){
							$dashboard .= '<br>Notes:<br>'.$status_note.'</td></tr>';
						}
					}
				}
			}
		}
	endwhile;
	wp_reset_postdata();
	$dashboard .= '</table></div>';
	return $dashboard;
}
add_shortcode('driver-dashboard','driver_dashboard');

function manage_delivery(){
	$delivery_post = $_GET['delivery_id'];
	$the_query = new WP_Query( array(
		'post_type'=> 'notification',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	$attempt = 0;
	while ($the_query -> have_posts()) : $the_query -> the_post();
		$metas = get_post_meta(get_the_ID(),'notification');
		foreach($metas as $meta){
			$meta = explode(":#",$meta);
			$notetype = $meta[0];
			if($notetype == 'setdeliverydriver'){
				if($meta[1]==$delivery_post){
					$attempt++;
				}
			}
		}
	endwhile;
	wp_reset_postdata();
	$driver = get_post_meta( $delivery_post, 'selected_driver', true );
	$user = wp_get_current_user();
	if($user->data->ID==$driver){
		$order_id = get_the_title($delivery_post);
		$order_id = explode(" ",$order_id);
		$order_id = explode("#",$order_id[1]);
		$order_id = $order_id[1];
		$order_data = getOrderDetailById($order_id);
		$address = $order_data['order']['billing_address'];
		$address_status = get_post_meta( $order_id, 'address_status' , true );
		if(!$address_status){
			$address_status = 0;
		}
		$user_id = get_customerorderid($order_id);
		$addresslatlon = get_user_meta( $user_id, 'Addressupdate#:'.$order_id , true );
		if($addresslatlon){
			$addresslatlon = explode('#:',$addresslatlon);
			$address_up = $addresslatlon[0].','.$addresslatlon[1];
		}else{
			$address_up = $address['address_1'].' '.$address['address_2'].' '.$address['city'].' '.$address['formated_state'];
		}
		$html = '<div class="hiddenarea3" style="display:none">'.$address_status.'</div><div class="hiddenarea2" style="display:none">'.get_theme_mod('startaddress').'</div><div class="hiddenarea" style="display:none">'.$address_up.'</div><p><strong>Meal Details</strong></p>';
		foreach($order_data['order']['line_items'] as $meal){
			$html .= '<p>' . $meal['name'] . ' x '.$meal['quantity'].'</p>';
		}
		$html .= '<p><strong>Address:</strong></p>' . $address['first_name'].' '.$address['last_name'] . '<br>'.$address['address_1'].'<br>'.$address['address_2'].'<br>'.$address['city'].'-'.$address['postcode'].'<br>'.$address['formated_state'].'<br>'.$address['formated_country'].'<br><a href="#" class="customer_address_update" data-order="'.$order_id.'">Update Address</a><br><br>';
		$html .= '<form action="'.get_site_url().'/driver-dashboard/" method="post"><p><strong>Status:</strong></p><select name="set_delivery_status"><option>Incomplete</option><option>Complete</option></select><br>';
		$html .= '<p><strong>Notes:</strong></p><input type="hidden" name="manage_delivery" /><input type="hidden" name="order_id" value="'.$order_id.'" /><input style="display:none" type="text" name="delivery_id" value="'.$delivery_post.'" /><input style="display:none" type="text" name="delivery_attempt" value="'.$attempt.'" /><input style="display:none" type="text" name="driver_id" value="'.$driver.'" /><textarea name="delivery_notes"></textarea><br><input type="submit" value="Submit"/></form>';
	}else{
		$html = 'Access Denied';
	}
	return $html;
}
add_shortcode('manage-delivery','manage_delivery');

function manage_delivery_post(){
	if(isset($_POST['manage_delivery'])){
		$order_id = $_POST['order_id'];
		$delivery_id = $_POST['delivery_id'];
		$driver_id = $_POST['driver_id'];
		$delivery_notes = $_POST['delivery_notes'];
		$set_delivery_status = $_POST['set_delivery_status'];
		$attempt = $_POST['delivery_attempt'];
		$timezone = date("Y-m-d h:m");
		add_post_meta( get_option( 'today_notification' ), 'notification', 'driverdeliveryupdate:#'.$delivery_id.':#'.$driver_id.':#'.$delivery_notes.':#'.$set_delivery_status.':#'.$attempt.':#'.$timezone, false );
		if($set_delivery_status=='Incomplete'){
			delete_post_meta($delivery_id, 'selected_driver');
		}
		update_user_meta( $driver_id, 'driver_availability', 'Free');
		add_post_meta( $delivery_id, 'driverdeliverystatusupdate',$set_delivery_status );
	}
	/*echo date('F j, Y g:i a', 1538554952);*/
}
add_action('init','manage_delivery_post');

function my_login_redirect( $redirect_to, $request, $user ) {
    global $user;
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {

        if ( in_array( 'driver', $user->roles ) ) {
            return get_site_url().'/driver-dashboard/';
        } else {
            return get_site_url().'/wp-admin/';
        }
    } else {
        return $redirect_to;
    }
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

function get_customerorderid($order_id){
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    return $user_id;
}

function get_order_address(){
	$order_id = $_REQUEST['order_id'];
	$order_data = getOrderDetailById($order_id);
	$address = $order_data['order']['billing_address'];
	$user_id = get_customerorderid($order_id);
	$addresslatlon = get_user_meta( $user_id, 'Addressupdate#:'.$order_id , true );
	if($addresslatlon){
		$addresslatlon = explode('#:',$addresslatlon);
		$address['lat'] = $addresslatlon[0];
		$address['lon'] = $addresslatlon[1];
	}else{
		$address['lat'] = '';
		$address['lon'] = '';
	}
	echo json_encode($address);
	wp_die();
}
add_action('wp_ajax_nopriv_get_order_address', 'get_order_address');
add_action('wp_ajax_get_order_address', 'get_order_address');

function update_customer_address(){
	$order_id = $_REQUEST['order_id'];
	$_billing_first_name = $_REQUEST['billing_first_name'];
	$_billing_last_name = $_REQUEST['billing_last_name'];
	$_billing_company = $_REQUEST['billing_company'];
	$_billing_address_1 = $_REQUEST['billing_address_1'];
	$_billing_address_2 = $_REQUEST['billing_address_2'];
	$_billing_city = $_REQUEST['billing_city'];
	$_billing_postcode = $_REQUEST['billing_postcode'];
	$billing_lat = $_REQUEST['billing_lat'];
	$billing_lon = $_REQUEST['billing_lon'];
	$user_id = get_customerorderid($order_id);
	update_user_meta( $user_id, 'Addressupdate#:'.$order_id, $billing_lat.'#:'.$billing_lon);
	update_post_meta( $order_id, '_billing_first_name', $_billing_first_name );
	update_post_meta( $order_id, '_billing_last_name', $_billing_last_name );
	update_post_meta( $order_id, '_billing_company', $_billing_company );
	update_post_meta( $order_id, '_billing_address_1', $_billing_address_1 );
	update_post_meta( $order_id, '_billing_address_2', $_billing_address_2 );
	update_post_meta( $order_id, '_billing_city', $_billing_city );
	update_post_meta( $order_id, '_billing_postcode', $_billing_postcode );
	wp_die();
}
add_action('wp_ajax_nopriv_update_customer_address', 'update_customer_address');
add_action('wp_ajax_update_customer_address', 'update_customer_address');
