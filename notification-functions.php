<?php 
class WPDocs_Notification_Meta_Box {
 
    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }
 
    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_notification'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox_notification' ), 10, 2 );
    }
 
    public function add_metabox_notification() {
        add_meta_box(
            'my-meta-box',
            __( 'Notifications', 'textdomain' ),
            array( $this, 'render_metabox_notification' ),
            'notification',
            'advanced',
            'default'
        );
 
    }

    public function render_metabox_notification( $post ) {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		?>
		<style>
		#edit-slug-box{display:none}
		#poststuff #woocommerce-order-notes .inside {margin: 0;padding: 0;}
		ul.order_notes {padding: 2px 0 0;}
		#poststuff #woocommerce-order-notes .inside ul.order_notes li {padding: 0 10px;}
		ul.order_notes li .note_content {padding: 10px;position: relative;}
		ul.order_notes li .note_content p {margin: 0;padding: 0;word-wrap: break-word;color:#fff}
		ul.order_notes li .note_content::after {content: '';display: block;position: absolute;bottom: -10px;left: 20px;width: 0;height: 0;border-width: 10px 10px 0 0;border-style: solid;}
		ul.order_notes li p.meta {padding: 10px;color: #999;margin: 0;font-size: 11px;}
		ul.order_notes li p.meta .exact-date {border-bottom: 1px dotted #999;}
		ul.order_notes li.note-type-mealready .note_content::after{border-color: #ffc04c transparent;}
		ul.order_notes li.note-type-createdelivery .note_content::after{border-color: #4c4cff transparent;}
		ul.order_notes li.note-type-setdeliverydriver .note_content::after{border-color: #4ca64c transparent;}
		ul.order_notes li.note-type-driverdeliveryupdate .note_content::after{border-color: #ff5a5a transparent;}
		ul.order_notes li.note-type-addressupdaterequest .note_content::after{border-color: #000 transparent;}
		.note-types .mealready span, ul.order_notes li.note-type-mealready .note_content{background:#ffc04c}
		.note-types .createdelivery span, ul.order_notes li.note-type-createdelivery .note_content{background:#4c4cff}
		.note-types .setdeliverydriver span, ul.order_notes li.note-type-setdeliverydriver .note_content{background:#4ca64c}
		.note-types .driverdeliveryupdate span, ul.order_notes li.note-type-driverdeliveryupdate .note_content{background:#ff5a5a}
		.note-types .addressupdaterequest span, ul.order_notes li.note-type-addressupdaterequest .note_content{background:#000}
		.note-types li{display:inline-block}
		.note-types li span {display: inline-block;width: 20px;height: 10px;margin: 0px 10px;}
		.note-types li:first-child span{margin-left:0px}
		.updateaddressadmin{cursor:pointer}
		</style>
		<div class="inside">
		<ul class="note-types">
		<li class="mealready"><span></span>Meal ready</li>
		<li class="createdelivery"><span></span>Ready for delivery</li>
		<li class="setdeliverydriver"><span></span>Delivery driver set</li>
		<li class="driverdeliveryupdate"><span></span>Driver Delivery Update</li>
		<li class="addressupdaterequest"><span></span>Address Update Request</li>
		</ul>
			<ul class="order_notes">
				<?php 
					$metas = get_post_meta(get_the_ID(),'notification');
					$metas = array_reverse($metas);
					foreach($metas as $meta){
						$meta = explode(":#",$meta);
						$notetype = $meta[0];
						if($notetype == 'mealready'){
							$mealready_order_id = explode("-",$meta[1]);
							$content = 'Meal is ready - Order #'.$mealready_order_id[2];
							$date = $meta[2];
						}else if($notetype == 'createdelivery'){
							$mealready_order_id = explode("-",$meta[1]);
							$content = 'Meal is ready for Delivery - Order #'.$mealready_order_id[2];
							$date = $meta[2];
						}else if($notetype == 'setdeliverydriver'){
							$mealready_order_id = explode(" ",get_the_title($meta[1]));
							$mealready_order_id = $mealready_order_id[1];
							$driver = get_userdata($meta[2]);
							$content = 'Meal Delivery assigned to '.$driver->display_name.' (Driver ID:'.$meta[2].') - Order '.$mealready_order_id;
							$date = $meta[4];
						}else if($notetype == 'driverdeliveryupdate'){
							$mealready_order_id = explode(" ",get_the_title($meta[1]));
							$mealready_order_id = $mealready_order_id[1];
							$driver = get_userdata($meta[2]);
							$content = 'Meal Delivery status update <strong>'.$meta[4].'</strong> by '.$driver->display_name.' (Driver ID:'.$meta[2].') - Order '.$mealready_order_id;
							if($meta[3]!=''){
								$content .= '<br><br><strong>Driver Notes: </strong><br>'.$meta[3];
							}
							$date = $meta[6];
						}else if($notetype == 'addressupdaterequest'){
							$address = explode("#:",$meta[2]);
							$content = 'Address Update Request<br><strong>Customer ID: </strong>'.$meta[1];
							$content .= '<br><strong>Order#: </strong>'.$meta[3].'<br><strong>Address: </strong><br>';
							$content .= '<span style="padding-left:15px;display:inline-block">'.$address[1].' '.$address[2].'<br>'.$address[3].'<br>'.$address[4].'<br>'.$address[5].'<br>'.$address[6].' - '.$address[7].'</span><br>';
							if(get_user_meta( $meta[1], 'order_address_update#:'.$meta[3].'#:'.$meta[5], true) !=0 ){
								$content .= '<a class="updateaddressadmin" data-attempt="'.$meta[5].'" data-customer="'.$meta[1].'" data-order="'.$meta[3].'">Update Address</a><span class="successmsg" style="display:none">Address has been updated.</span>';
							}else{
								$content .= '<span class="successmsg">Address has been updated.</span>';
							}
							$date = $meta[4];
						}
				?>
				<li class="note note-type-<?php echo $notetype;?>">
					<div class="note_content">
						<p><?php echo $content; ?></p>
					</div>
					<p class="meta">
						<abbr class="exact-date" title="18-08-24 11:05:51">added on <?php echo $date;?></abbr>
					</p>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php
    }
 
    public function save_metabox_notification( $post_id, $post ) {
        
    }
}
 
new WPDocs_Notification_Meta_Box();

add_action('wp_dashboard_setup', 'dashboard_notification_widgets');
  
function dashboard_notification_widgets() {
global $wp_meta_boxes;
$timezone = date("Y-m-d");
wp_add_dashboard_widget('custom_help_widget', 'Todays Notifications - '.$timezone, 'dashboard_notifications');
}
 
function dashboard_notifications() {?>
	<style>
		#poststuff #woocommerce-order-notes .inside {margin: 0;padding: 0;}
		ul.order_notes {padding: 2px 0 0;}
		#poststuff #woocommerce-order-notes .inside ul.order_notes li {padding: 0 10px;}
		ul.order_notes li .note_content {padding: 10px;position: relative;}
		ul.order_notes li .note_content p {margin: 0;padding: 0;word-wrap: break-word;color:#fff}
		ul.order_notes li .note_content::after {content: '';display: block;position: absolute;bottom: -10px;left: 20px;width: 0;height: 0;border-width: 10px 10px 0 0;border-style: solid;}
		ul.order_notes li p.meta {padding: 10px;color: #999;margin: 0;font-size: 11px;}
		ul.order_notes li p.meta .exact-date {border-bottom: 1px dotted #999;}
		ul.order_notes li.note-type-mealready .note_content::after{border-color: #ffc04c transparent;}
		ul.order_notes li.note-type-createdelivery .note_content::after{border-color: #4c4cff transparent;}
		ul.order_notes li.note-type-setdeliverydriver .note_content::after{border-color: #4ca64c transparent;}
		ul.order_notes li.note-type-driverdeliveryupdate .note_content::after{border-color: #ff5a5a transparent;}
		ul.order_notes li.note-type-addressupdaterequest .note_content::after{border-color: #000 transparent;}
		.note-types .mealready span, ul.order_notes li.note-type-mealready .note_content{background:#ffc04c}
		.note-types .createdelivery span, ul.order_notes li.note-type-createdelivery .note_content{background:#4c4cff}
		.note-types .setdeliverydriver span, ul.order_notes li.note-type-setdeliverydriver .note_content{background:#4ca64c}
		.note-types .driverdeliveryupdate span, ul.order_notes li.note-type-driverdeliveryupdate .note_content{background:#ff5a5a}
		.note-types .addressupdaterequest span, ul.order_notes li.note-type-addressupdaterequest .note_content{background:#000}
		.note-types li{display:block}
		.note-types li span {display: inline-block;width: 20px;height: 10px;margin: 0px 10px;}
		.note-types li span{margin-left:0px}
		.updateaddressadmin{cursor:pointer}
		</style>
		<div class="inside">
		<ul class="note-types">
		<li class="mealready"><span></span>Meal ready</li>
		<li class="createdelivery"><span></span>Ready for delivery</li>
		<li class="setdeliverydriver"><span></span>Delivery driver set</li>
		<li class="driverdeliveryupdate"><span></span>Driver Delivery Update</li>
		<li class="addressupdaterequest"><span></span>Address Update Request</li>
		</ul>
			<ul class="order_notes">
				<?php 
					$metas = get_post_meta(get_option( 'today_notification' ),'notification');
					$metas = array_reverse($metas);
					$counter = 1;
					foreach($metas as $meta){
						if($counter>10){
							break;
						}
						$counter++;
						$meta = explode(":#",$meta);
						$notetype = $meta[0];
						if($notetype == 'mealready'){
							$mealready_order_id = explode("-",$meta[1]);
							$content = 'Meal is ready - Order #'.$mealready_order_id[2];
							$date = $meta[2];
						}else if($notetype == 'createdelivery'){
							$mealready_order_id = explode("-",$meta[1]);
							$content = 'Meal is ready for Delivery - Order #'.$mealready_order_id[2];
							$date = $meta[2];
						}else if($notetype == 'setdeliverydriver'){
							$mealready_order_id = explode(" ",get_the_title($meta[1]));
							$mealready_order_id = $mealready_order_id[1];
							$driver = get_userdata($meta[2]);
							$content = 'Meal Delivery assigned to '.$driver->display_name.' (Driver ID:'.$meta[2].') - Order '.$mealready_order_id;
							$date = $meta[4];
						}else if($notetype == 'driverdeliveryupdate'){
							$mealready_order_id = explode(" ",get_the_title($meta[1]));
							$mealready_order_id = $mealready_order_id[1];
							$driver = get_userdata($meta[2]);
							$content = 'Meal Delivery status update <strong>'.$meta[4].'</strong> by '.$driver->display_name.' (Driver ID:'.$meta[2].') - Order '.$mealready_order_id;
							if($meta[3]!=''){
								$content .= '<br><br><strong>Driver Notes: </strong><br>'.$meta[3];
							}
							$date = $meta[6];
						}else if($notetype == 'addressupdaterequest'){
							$address = explode("#:",$meta[2]);
							$content = 'Address Update Request<br><strong>Customer ID: </strong>'.$meta[1];
							$content .= '<br><strong>Order#: </strong>'.$meta[3].'<br><strong>Address: </strong><br>';
							$content .= '<span style="padding-left:15px;display:inline-block">'.$address[1].' '.$address[2].'<br>'.$address[3].'<br>'.$address[4].'<br>'.$address[5].'<br>'.$address[6].' - '.$address[7].'</span><br>';
							if(get_user_meta( $meta[1], 'order_address_update#:'.$meta[3].'#:'.$meta[5], true) !=0 ){
								$content .= '<a class="updateaddressadmin" data-attempt="'.$meta[5].'" data-customer="'.$meta[1].'" data-order="'.$meta[3].'">Update Address</a><span class="successmsg" style="display:none">Address has been updated.</span>';
							}else{
								$content .= '<span class="successmsg">Address has been updated.</span>';
							}
							$date = $meta[4];
						}
				?>
				<li class="note note-type-<?php echo $notetype;?>">
					<div class="note_content">
						<p><?php echo $content; ?></p>
					</div>
					<p class="meta">
						<abbr class="exact-date" title="18-08-24 11:05:51">added on <?php echo $date;?></abbr>
					</p>
				</li>
					<?php } ?>
			</ul>
		</div>
<?php }

function update_address_admin(){
    $customer_id = $_POST['customer_id'];
	$order_id = $_POST['order_id'];
	$attempt = $_POST['attempt'];
	$metas = get_post_meta(get_option( 'today_notification' ),'notification');
	$metas = array_reverse($metas);
	foreach($metas as $meta){
		$meta = explode(":#",$meta);
		$notetype = $meta[0];
		if($notetype == 'addressupdaterequest'){
			if($meta[1].$meta[3]== $customer_id.$order_id && $meta[5]==$attempt){
				$data = explode("#:",$meta[2]);
			}
		}
	}
	$_billing_first_name = $data[1];
	$_billing_last_name = $data[2];
	$_billing_company = $data[3];
	$_billing_address_1 = $data[4];
	$_billing_address_2 = $data[5];
	$_billing_city = $data[6];
	$_billing_postcode = $data[7];
	update_post_meta( $order_id, '_billing_first_name', $_billing_first_name );
	update_post_meta( $order_id, '_billing_last_name', $_billing_last_name );
	update_post_meta( $order_id, '_billing_company', $_billing_company );
	update_post_meta( $order_id, '_billing_address_1', $_billing_address_1 );
	update_post_meta( $order_id, '_billing_address_2', $_billing_address_2 );
	update_post_meta( $order_id, '_billing_city', $_billing_city );
	update_post_meta( $order_id, '_billing_postcode', $_billing_postcode );
	update_user_meta( $customer_id, 'order_address_update#:'.$order_id.'#:'.$attempt, 0);
    exit();
}
add_action('wp_ajax_update_address_admin', 'update_address_admin');
