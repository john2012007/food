jQuery(document).ready(function($) {
	jQuery('.setmealready').click(function() {
        var mealID = jQuery(this).data('meal');
        var postID = jQuery(this).data('post');
        var currentMeal = jQuery(this);
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: 'kitchen_meal_update',
                meal_id: mealID,
                post_id: postID
            }
        }).done(function(msg) {
            window.location.href = msg;
			currentMeal.parent().find('.orderstatus').html('Meal Ready');
            currentMeal.parent().parent().find('.createmealdelivery').removeAttr("disabled");
            currentMeal.hide();
        });
    });

    jQuery('.createmealdelivery').click(function() {
        var mealID = jQuery(this).data('meal');
        var currentMeal = jQuery(this);
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: 'create_meal_delivery',
                meal_id: mealID
            }
        }).done(function(msg) {
            window.location.href = msg;
        });
    });

    jQuery('.updateaddressadmin').click(function(e) {
        e.preventDefault();
        var customerID = jQuery(this).data('customer');
        var orderID = jQuery(this).data('order');
        var attempt = jQuery(this).data('attempt');
        var currentUpdate = jQuery(this);
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: 'update_address_admin',
                customer_id: customerID,
                order_id: orderID,
                attempt: attempt
            }
        }).done(function(msg) {
            currentUpdate.hide();
            currentUpdate.parent().find('.successmsg').css('display', 'block');
        });
    });

    jQuery('.setdeliverydriver').click(function() {
        var deliveryID = jQuery(this).data('delivery');
        var driverID = jQuery('.driverselect').val();
        if (driverID != 0) {
            var order = jQuery(this);
			order.attr('disabled', 'disabled');
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: 'set_delivery_driver',
                    delivery_id: deliveryID,
                    driver_id: driverID
                }
            }).done(function(msg) {
                jQuery('.driverselect').hide();
                jQuery('.drivername').html(msg);
                jQuery('.drivername').css('color', '#555');
            });
        } else {
            jQuery('.drivername').html('Please select driver');
            jQuery('.drivername').css('color', 'red');
        }
    });
	
	jQuery('.updatedates').click(function(e){
		e.preventDefault();
		$this = $(this);
		if ($this.hasClass("disabled")) {
			return;
		}else{
			$this.addClass('disabled');
		}
		loader = $(this).parent().find( ".loading-wrap" );
		loader.html('<div class="loadersmall"></div>');
		item_id = $(this).data('id');
		startdate = $(this).parent().parent().find('.startdate').val();
		enddate = $(this).parent().parent().find('.enddate').val();
		order_id = $(this).data('order_id');
		product_id = $(this).data('product');
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'updatedates_order' , item_id : item_id, startdate:startdate, enddate:enddate, order_id:order_id, product_id:product_id}
		}).done(function( data ) {
			location.reload();
		});
	});
	
	jQuery('.datepickerstart').datepicker({ 
		dateFormat: 'mm/dd/yy',
		minDate: 1,
		onSelect: function(selected) {
			jQuery(this).parent().parent().find('.datepickerend').datepicker("option","minDate", selected);
		}
	});
	
	jQuery('.datepickerend').datepicker({
		dateFormat: 'mm/dd/yy',
		minDate: 1,
		onSelect: function(selected) {
			jQuery(this).parent().parent().find('.datepickerstart').datepicker("option","maxDate", selected);
		}
	});
	
	jQuery('#createuser #role').change(function(e){
		var userRole = $('#role').val();
		if(userRole=='driver'){
			jQuery('.driverarea').addClass('active');
		}else{
			jQuery('.driverarea').removeClass('active');
		}
	})
	
	jQuery('.submitproductstock').click(function(e){
		e.preventDefault();
		rows = jQuery('.product-stock-wrap tr');
		selectedDate = jQuery('.submitproductstock').data('date');
		string = '';
		rows.each(function() {
		  if(jQuery(this).find('input').length){
			id = jQuery(this).find('input').data('id');
			if(jQuery(this).find('input').val()){
				qty = jQuery(this).find('input').val();
			}else{
				qty = 10;
			}
			string += id + ',' + qty + '#:'
		  }
		});
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'manage_product_stock' , string : string, selected_date:selectedDate}
		}).done(function( data ) {
			jQuery('#submit').trigger('click');
		});
	})
	
	jQuery('#all_meal_select').change(function(){
		if(jQuery(this).prop("checked") == true){
			jQuery('.meal_checkbox').prop('checked', true);
        }else{
			jQuery('.meal_checkbox').prop('checked', false);
		}
	})
	
	jQuery('.bulksetmealready').click(function(){
		var meals = '';
		var post = '';
		date = jQuery(this).data('date');
		jQuery('.meal_checkbox').each(function () {
			post = jQuery(this).data('post');
			if(this.checked){
				meals = meals + jQuery(this).data('meal')+',';
			}
		});
		if(meals){
			var mealID = meals;
			var postID = post;
			var currentMeal = jQuery(this);
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'bulk_kitchen_meal_update',
					meal_id: mealID,
					post_id: postID,
					date_selected: date
				}
			}).done(function(msg) {
				window.location.href = msg;
			});
		}else{
			alert('Please select atleast one meal to set meal ready.');
		}
	})
});
