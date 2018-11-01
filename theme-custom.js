jQuery(document).ready(function(){

   // For Search Icon Toggle effect added at the top
   jQuery('.search-top').click(function(){
      jQuery('#masthead .search-form-top').toggle();
   });
   
    jQuery(".trigger_popup_fricc").click(function(){
       jQuery('.hover_bkgr_fricc').show();
    });
    jQuery('.popupCloseButton').click(function(){
        jQuery('.hover_bkgr_fricc').hide();
    });

   // For Scroll to top button
   jQuery('#scroll-up').hide();
   jQuery(function () {
      jQuery(window).scroll(function () {
         if (jQuery(this).scrollTop() > 1000) {
            jQuery('#scroll-up').fadeIn();
         } else {
            jQuery('#scroll-up').fadeOut();
         }
      });
      jQuery('a#scroll-up').click(function () {
         jQuery('body,html').animate({
            scrollTop: 0
         }, 800);
         return false;
      });
   });
   
   jQuery( '[name="custom-options[startdate]"]' ).datepicker({ minDate: 0});
   
   jQuery( ".input-text.custom-options.custom_field" ).attr("autocomplete", "off");
   
   jQuery('.woocommerce-form-register .button').click(function(e){
	    var mobNum = jQuery('#reg_username').val();
        var filter = /^\d*(?:\.\d{1,2})?$/;
		jQuery('.reg_err_msg').remove();jQuery('#reg_username').css('border','1px solid #aaa');
          if (filter.test(mobNum)) {
            if(mobNum.length==10){
              
             } else {
				jQuery('#reg_username').css('border','1px solid red');
				jQuery('.woocommerce-form-register .form-row-username').append('<p class="reg_err_msg">Please put 10  digit mobile number</p>');
				jQuery('.reg_err_msg').css({"color": "red", "padding-top": "10px"});
               e.preventDefault();
              }
            }
            else {
			jQuery('#reg_username').css('border','1px solid red');
			  jQuery('.woocommerce-form-register .form-row-username').append('<p class="reg_err_msg">Not a valid number</p>');
			  jQuery('.reg_err_msg').css({"color": "red", "padding-top": "10px"});
              e.preventDefault();
           }
   })

});
