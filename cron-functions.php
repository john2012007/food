<?php 
function today_meal_orders(){
	$the_query = new WP_Query( array(
		'post_type'=> 'subscription',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	));
	$order_id = array();
	while ($the_query -> have_posts()) : $the_query -> the_post();
		$s = new Subscriptio;
		$subscription = $s->load_from_cache('subscriptions', get_the_ID());
		if($subscription->status == 'active'){
			if($subscription->last_order_id){
				foreach($subscription->all_order_ids as $soid){
					$parent = $soid;
				}
				$area = get_post_meta( $parent, 'billing_customer_area' , true );
				$verify = get_post_meta( $parent, 'address_status' , true );
				foreach($subscription->all_order_ids as $soid){
					update_post_meta( $soid, 'billing_customer_area', $area );
					if(!get_post_meta( $soid, 'address_status' , true )){
						update_post_meta( $soid, 'address_status', $verify );
					}
				}
				$order_data = getOrderDetailById($subscription->last_order_id);
				if($order_data['order']['status'] == 'completed'){
					$order_id[] = $subscription->last_order_id;
				}
			}
		}
	endwhile;
	wp_reset_postdata();
	$order_id = array_unique($order_id);
	$order_ids = '';
	foreach($order_id as $oid){
		$order_ids .= $oid.',';
	}
	if($order_ids){
		$title = 'Today orders - '.date("Y-m-d");
		$page = get_page_by_title( $title, OBJECT, 'kitchen' );
		$order_ids = rtrim($order_ids,',');
		if(	$page ){
			$order_ids;
			update_post_meta( $page->ID, 'today_orders', $order_ids );
		}else{
			$new_post = array(
			'post_title'    => $title,
			'post_status'   => 'publish',          
			'post_type'     => 'kitchen' 
			);
			$pid = wp_insert_post($new_post);
			$meta_key = 'today_orders';
			add_post_meta($pid, $meta_key, $order_ids, true);
			$title = 'Today notifications - '.date("Y-m-d");
			$new_post = array(
			'post_title'    => $title,
			'post_status'   => 'publish',          
			'post_type'     => 'notification' 
			);
			$pid = wp_insert_post($new_post);
			update_option( 'today_notification', $pid );
			//update_option( 'fullcalendar#:2018-09-06', '227,22#:220,10#:27,0#:9,5' );
			$today = date("Y-m-d");
			$options = wp_load_alloptions();
			foreach($options as $key=>$val){
				$checkKey = explode("#:", $key);
				if($checkKey[0]=='fullcalendar'){
					if($today == $checkKey[1]){
						$values = explode("#:",$val);
						foreach($values as $value){
							$value = explode(",",$value);
							$product_id = $value[0];
							$qty = $value[1];
							$product = new WC_Product( $product_id );
							$product->set_stock($qty);
						}
					}
				}
			}
		}
	}
}
add_action('generate_today_orders','today_meal_orders');
add_action('init','today_meal_orders');
