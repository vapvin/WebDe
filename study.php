<?php
(function($){
    var billingPostcodeWrap=$('.woocommerce-billing-fields__field-wrapper .validate-postcode .woocommerce-input-wrapper');
    var shippingPostcodeWrap=$('.woocommerce-shipping-fields__field-wrapper .validate-postcode .woocommerce-input-wrapper');
    billingPostcodeWrap.append('<button id="billing-postcode-btn" class="postcode-btn button"><?php _e('우편번호 찾기', 'twentynineteen-child')?></button>');
    shippingPostcodeWrap.append('<button id="shipping-postcode-btn" class="postcode-btn button"><?php _e('우편번호 찾기', 'twentynineteen-child')?></button>');
    
    var billingPostcodeBtn=$('#billing-postcode-btn');
    var shippingPostcodeBtn=$('#shipping-postcode-btn');
    
    billingPostcodeBtn.on('click', function(e){
        e.preventDefault();
        billing_search_postcode();      
    });
    
    shippingPostcodeBtn.on('click', function(e){
        e.preventDefault();
        shipping_search_postcode();      
    });

	//다른 주소 배송
    var shipDiffAdress = $('#ship-to-different-address label');
    shipDiffAdress.on('change', function(e){
        var is_checked = $('#ship-to-different-address-checkbox').is(':checked');
		if(is_checked == false){
			$('.woocommerce-shipping-fields__field-wrapper input').not('input[type="radio"]').each(function(){
				$(this).val('');
			});
		}
    });

	//국내/해외배송
    var billingShppingOption = $('input[name="billing_shipping_option"]');
    var shippingShppingOption = $('input[name="shipping_shipping_option"]');

    $(document).ready(function(){
		billing_shipping_option($('input[name="billing_shipping_option"]:checked').val(), '');
	    shipping_shipping_option($('input[name="shipping_shipping_option"]:checked').val(), '');

	    //국가선택 이벤트
	    $(document.body).on('country_to_state_changing', function(event, country, wrapper) {
	    	//우편번호를 무조건 첫번째로
	    	//console.log($('input[name="billing_shipping_option"]:checked').val());
	    	if($('input[name="billing_shipping_option"]:checked').val() == 'domestic_shipping'){
	    		setTimeout(function() {
	            	$('#billing_postcode_field').insertBefore($('#billing_address_1_field'));
	            }, 50);
	    	}
	    	
	    	if($('input[name="shipping_shipping_option"]:checked').val() == 'domestic_shipping'){
	    		setTimeout(function() {
	            	$('#shipping_postcode_field').insertBefore($('#shipping_address_1_field'));
	            }, 50);
	    	}        
            
            var billing = wrapper.hasClass('woocommerce-billing-fields');
            var shipping = wrapper.hasClass('woocommerce-shipping-fields');

            if(billing){
            	billing_shipping_option('', country);
            	
            }
            if(shipping){
            	shipping_shipping_option('', country);
            }
        });

        
	});

?>