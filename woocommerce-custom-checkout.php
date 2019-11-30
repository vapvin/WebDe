<?php

/**체크아웃**/
//체크아웃필드 추가삭제
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
	//청구필드
	unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	//	unset($fields['billing']['billing_country']);
	//	unset($fields['billing']['billing_address_1']);
	//	unset($fields['billing']['billing_address_2']);
	//	unset($fields['billing']['billing_city']);
	//	unset($fields['billing']['billing_state']);
	//	unset($fields['billing']['billing_postcode']);
	$fields['billing']['billing_email']['priority'] = 35;
	$fields['billing']['billing_phone']['priority'] = 38;
	
	//배송필드
	unset($fields['shipping']['shipping_last_name']);
	unset($fields['shipping']['shipping_company']);
	//	unset($fields['shipping']['shipping_country']);
	//	unset($fields['shipping']['shipping_address_1']);
	//	unset($fields['shipping']['shipping_address_2']);
	//	unset($fields['shipping']['shipping_city']);
	//	unset($fields['shipping']['shipping_state']);
	//	unset($fields['shipping']['shipping_postcode']);
	
	//주문메모 필드
	$fields['order']['order_comments']['placeholder'] = "";
	$fields['order']['order_comments']['default'] = "1. 해외 배송을 원하시는 경우 거주하시는 해외 현지 주소를 적어주세요.
2. 주문 관련 메시지나 배송 관련 메모를 남겨주세요.
예) 갑티슈는 안 보내셔도 됩니다
예) 경비실에 맡겨 주세요.";
	
	//청구필드 옵션
	$fields['billing']['billing_shipping_option'] = array(
		 'type' => 'radio',
		 'class' => array( 'form-row-wide' ),
		 'options' => array(
			 'domestic_shipping' => __('국내주소', 'twentynineteen-child'),
			 'international_shipping' => __('해외주소', 'twentynineteen-child'),
		 ),
		 'default' => 'domestic_shipping',
		 'priority' => '39',
	 );
	
	 //배송필드 옵션
	 $fields['shipping']['shipping_shipping_option'] = array(
		 'type' => 'radio',
		 'class' => array( 'form-row-wide' ),
		 'options' => array(
			 'domestic_shipping' => __('국내주소', 'twentynineteen-child'),
			 'international_shipping' => __('해외주소', 'twentynineteen-child'),
		 ),
		 'default' => 'domestic_shipping',
		 'priority' => '39',
	 );
	
	//배송필드에 연락처 추가
	$fields['shipping']['shipping_phone1'] = array(
			'label' => __('Phone', 'woocommerce'),
			'required' => true,
			'class' => array( 'form-row-wide' ),
			'priority' => 38,
	);
	
	return $fields;
}

function shipping_phone_checkout_display( $order ){
 echo '<p>'. get_post_meta( $order->get_id(), '_shipping_phone1', true ) . '</p>';
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'shipping_phone_checkout_display' );

//결제필드 필수 해제
add_filter( 'woocommerce_billing_fields', 'remove_required_billing_fields', 999, 1 );
function remove_required_billing_fields( $address_fields ) {
	//	$address_fields['billing_country']['required'] = false;
	//	$address_fields['billing_address_1']['required'] = false;
	$address_fields['billing_address_2']['required'] = false;
	$address_fields['billing_city']['required'] = false;
	$address_fields['billing_state']['required'] = false;
	//	$address_fields['billing_postcode']['required'] = false;
	return $address_fields;
}

//배송필드 필수 해제
add_filter( 'woocommerce_shipping_fields', 'remove_required_shipping_fields', 999, 1 );
function remove_required_shipping_fields( $address_fields ) {
	//	$address_fields['shipping_country']['required'] = false;
	//	$address_fields['shipping_address_1']['required'] = false;
	$address_fields['shipping_address_2']['required'] = false;
	$address_fields['shipping_city']['required'] = false;
	$address_fields['shipping_state']['required'] = false;
	//	$address_fields['shipping_postcode']['required'] = false;
	return $address_fields;
}


//주소필드 순서 변경
add_filter( 'woocommerce_default_address_fields', 'custom_entire_checkout_fields' );
function custom_entire_checkout_fields( $fields ) {
	
	// default priorities:  기본 우선순위
	// 'first_name' - 10  (이름)
	// 'last_name' - 20  (성)
	// 'company' - 30 (회사명)
	// 'country' - 40 (국가)
	// 'address_1' - 50 (집 번호 또는 거리명)
	// 'address_2' - 60 (아파트, 동, 호수 기타)
	// 'city' - 70 (기본 주소)
	// 'state' - 80 (주)
	// 'postcode' - 90 (우편번호)
	
	//$fields['postcode']['priority'] = 45;
	
	return $fields;
}

//국가, 주 필드 드롭다운 스타일 삭제
add_action( 'wp_enqueue_scripts', function() {
	//wp_dequeue_style( 'select2' );
	wp_dequeue_script( 'select2');
	wp_dequeue_script( 'selectWoo' );
}, 11 );
	
	
//우편번호
function search_postcode(){
	if(!is_checkout()) return; //체크아웃, 마이페이지만 적용
			//		$location = WC_Geolocation::geolocate_ip();
			//		$country = $location['country'];
?>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
function billing_search_postcode(){
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을 때 실행할 코드를 작성하는 부분.
 
            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = ''; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수
 
            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;
 
            }
            else { // 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }
 
            // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
            if(data.userSelectedType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }
 
            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('billing_postcode').value = data.zonecode;
            document.getElementById("billing_address_1").value = fullAddr;
            document.getElementById("billing_city").value = '';
            // 커서를 상세주소 필드로 이동한다.
            document.getElementById("billing_address_2").focus();
        }
    }).open();
}

function shipping_search_postcode(){
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을 때 실행할 코드를 작성하는 부분.
 
            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = ''; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수
 
            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;
 
            }
            else { // 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }
 
            // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
            if(data.userSelectedType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }
 
            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('shipping_postcode').value = data.zonecode;
            document.getElementById("shipping_address_1").value = fullAddr;
            document.getElementById("shipping_city").value = '';
            // 커서를 상세주소 필드로 이동한다.
            document.getElementById("shipping_address_2").focus();
        }
    }).open();
}
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

    //배송지역 체크 시 이벤트
    billingShppingOption.on('change', function(){
    	billing_shipping_option($(this).val(), '');
    	$('body').trigger('update_checkout');
    });
    shippingShppingOption.on('change', function(){
    	shipping_shipping_option($(this).val(), '');
    	$('body').trigger('update_checkout');
    });

	/**Select 옵션**/
    function set_original_select($select) {
        if ($select.data("originalHTML") == undefined) {
            $select.data("originalHTML", $select.html());
        } // If it's already there, don't re-set it
    }

    function remove_options($select, $options) {
    	set_original_select($select);
        $options.remove();
    }

    function restore_options($select) {
        var ogHTML = $select.data("originalHTML");
        if (ogHTML != undefined) {
            $select.html(ogHTML);
        }
    }
    /**Select 옵션**/
    
    //배송지역 체크 시 이벤트
    function billing_shipping_option($checked, $country){
    	if($checked == 'domestic_shipping' || $country == 'KR'){	//국내주소
    		$('#billing_postcode_field').show();
    		$('#billing_postcode').prop("readonly", true);
    		$('#billing-postcode-btn').css('visibility', 'visible');
    	    $('#billing_address_1').prop("readonly", true);
    	    $('#billing_country_field').addClass('hidden');
    		$('#billing_city_field').addClass('hidden');
    		$('#billing_state_field').addClass('hidden');
    		
    		restore_options($('#billing_country'));
    		$('#billing_country').val('KR');

    		//필드라벨
    		setTimeout(function() {
    			$('#billing_postcode_field').insertBefore($('#billing_address_1_field'));
    			$('#billing_postcode_field label').contents().first()[0].textContent='<?php echo __('ZIP', 'woocommerce')?> ';
    			$('#billing_address_1_field label').contents().first()[0].textContent='<?php echo __('Street address', 'woocommerce')?> ';
    			$('#billing_address_2_field label').addClass('screen-reader-text');
    			$('#billing_address_1').attr("placeholder", "<?php echo __('House number and street name', 'woocommerce')?>");
    			$('#billing_address_2').attr("placeholder", "<?php echo __('Apartment, suite, unit etc. (optional)', 'woocommerce')?>");
    		}, 50);
    	}
    	
    	if($checked == 'international_shipping'){	//해외주소
    		$('#billing_postcode_field').show();
    		$('#billing_postcode').attr("readonly", false);
    		$('#billing-postcode-btn').css('visibility', 'hidden');
    	    $('#billing_address_1').attr("readonly", false);
    	    $('#billing_country_field').removeClass('hidden');
    		$('#billing_city_field').removeClass('hidden');
    		$('#billing_state_field').removeClass('hidden');
    		
    		remove_options($('#billing_country'), $('#billing_country option[value*="KR"]'));
    		$('#billing_country').val('');
    	}

    	if($checked != 'domestic_shipping' && $country != 'KR'){	//해외주소
    		//필드라벨
    		setTimeout(function() {
    			$('#billing_postcode_field label').contents().first()[0].textContent='<?php echo __('우편 번호(ZIP/Postal code)', 'twentynineteen-child')?> ';
    			$('#billing_address_1_field label').contents().first()[0].textContent='<?php echo __('주소 1(Address line 1)', 'twentynineteen-child')?> ';
    			$('#billing_address_2_field label').removeClass('screen-reader-text');
    			$('#billing_address_2_field label').contents().first()[0].textContent='<?php echo __('주소 2(Address line 2)', 'twentynineteen-child')?> ';
    			$('#billing_address_1').attr("placeholder", "<?php echo __('House number and street name', 'twentynineteen-child')?>");
    			$('#billing_address_2').attr("placeholder", "<?php echo __('Apartment, suite, unit etc. (optional)', 'twentynineteen-child')?>");
    		}, 50);
    	}
    	
    }
    
    function shipping_shipping_option($checked, $country){
    	if($country == 'KR' || $checked == 'domestic_shipping'){
    		$('#shipping_postcode_field').show();
    		$('#shipping_postcode').prop("readonly", true);
    		$('#shipping-postcode-btn').css('visibility', 'visible');
    	    $('#shipping_address_1').prop("readonly", true);
    	    $('#shipping_country_field').addClass('hidden');
    		$('#shipping_city_field').addClass('hidden');
    		$('#shipping_state_field').addClass('hidden');

    		restore_options($('#shipping_country'));
    		$('#shipping_country').val('KR');

    		//필드라벨
    		setTimeout(function() {
    			$('#shipping_postcode_field').insertBefore($('#shipping_address_1_field'));
    			$('#shipping_postcode_field label').contents().first()[0].textContent='<?php echo __('ZIP', 'woocommerce')?> ';
    			$('#shipping_address_1_field label').contents().first()[0].textContent='<?php echo __('Street address', 'woocommerce')?> ';
    			$('#shipping_address_2_field label').addClass('screen-reader-text');
    			$('#shipping_address_1').attr("placeholder", "<?php echo __('House number and street name', 'woocommerce')?>");
    			$('#shipping_address_2').attr("placeholder", "<?php echo __('Apartment, suite, unit etc. (optional)', 'woocommerce')?>");
    		}, 50);
    	}
    	
    	if($checked == 'international_shipping'){
    		$('#shipping_postcode_field').show();
    		$('#shipping_postcode').attr("readonly", false);
    		$('#shipping-postcode-btn').css('visibility', 'hidden');
    	    $('#shipping_address_1').attr("readonly", false);
    	    $('#shipping_country_field').removeClass('hidden');
    		$('#shipping_city_field').removeClass('hidden');
    		$('#shipping_state_field').removeClass('hidden');

    		remove_options($('#shipping_country'), $('#shipping_country option[value*="KR"]'));
    		$('#shipping_country').val('');
    	}

    	if($checked != 'domestic_shipping' && $country != 'KR'){
    		//필드라벨
    		setTimeout(function() {
    			$('#shipping_postcode_field label').contents().first()[0].textContent='<?php echo __('우편 번호(ZIP/Postal code)', 'twentynineteen-child')?> ';
    			$('#shipping_address_1_field label').contents().first()[0].textContent='<?php echo __('주소 1(Address line 1)', 'twentynineteen-child')?> ';
    			$('#shipping_address_2_field label').removeClass('screen-reader-text');
    			$('#shipping_address_2_field label').contents().first()[0].textContent='<?php echo __('주소 2(Address line 2)', 'twentynineteen-child')?> ';
    			$('#shipping_address_1').attr("placeholder", "<?php echo __('House number and street name', 'twentynineteen-child')?>");
    			$('#shipping_address_2').attr("placeholder", "<?php echo __('Apartment, suite, unit etc. (optional)', 'twentynineteen-child')?>");
    		}, 50);
    	}
    }

    
})(jQuery);
</script>
<?php
}
add_action('wp_footer', 'search_postcode');

//이용약관/개인정보 처리방침
function checkout_accept_policy(){
?>
<div id="policy">
	<div class="accept-policy-content">
		<input type="checkbox" name="accept_policy" id="accept-policy">
		<label for="accept_policy">
			<span class="policy-title"><?php _e('상기 정보를 확인하였으며, 결제진행에 동의합니다.', 'twentynineteen-child')?><span class="required">*</span>
		</label>
		<div class="policy-error" style="display:none;">결제진행에 동의해주세요.</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$("#place_order").on('click', function(){
		if($('input:checkbox[id="accept-policy"]').is(":checked") == false){
			$('.policy-error').show();
			return false;
	    };
	});
});
</script>
<?php
}
add_action('woocommerce_checkout_terms_and_conditions', 'checkout_accept_policy');

/**체크아웃(결제)**/
//비로그인시 체크아웃 리다이렉트
function redirect_guest_checkout_to_login(){
	if(!is_user_logged_in() && is_checkout()){
		wp_redirect( home_url('/login/') );
		exit;
	}
}
add_action('template_redirect', 'redirect_guest_checkout_to_login');

//체크아웃 에러메세지
add_filter( 'woocommerce_form_field', 'checkout_fields_in_label_error', 10, 4 );
function checkout_fields_in_label_error( $field, $key, $args, $value ) {
	if ( strpos( $field, '</p>' ) !== false && $args['required'] ) {
		$error = '<span class="error" style="display:none">';
		$error .= sprintf( __( '%s is a required field.', 'woocommerce' ), $args['label'] );
		$error .= '</span>';
		$field = substr_replace( $field, $error, strpos( $field, '</p>' ), 0);
	}
	return $field;
}

add_action( 'wp_footer', 'misha_checkout_js' );
function misha_checkout_js(){
	// we need it only on our checkout page
	if( !is_checkout() ) return;
	?>
	<script>
	(function($){
		$(document.body).on('checkout_error', function(){
			jQuery( 'html, body' ).stop();
			jQuery( 'html, body' ).animate({scrollTop:0}, 600);
		});
	})(jQuery);
	</script>
	<?php
}

//Thank You 메세지
add_filter( 'woocommerce_thankyou_order_received_text', 'thank_you_title', 20, 2 );
function thank_you_title( $thank_you_title, $order ){
	$title=__('후원이 완료되었습니다.', 'twentynineteen-child');
	$description = __('정의로운 작전에 참여해주셔서 감사합니다.', 'twentynineteen-child');
	return "<h3 class='order-title'>$title</h3><p class='order-decription'>$description</p>";
	
}

//입금계좌정보 html
function get_bacs_account_details_html( $echo = true, $type = 'list' ) {
	ob_start();
	
	$gateway    = new WC_Gateway_BACS();
	$country    = WC()->countries->get_base_country();
	$locale     = $gateway->get_country_locale();
	$bacs_info  = get_option( 'woocommerce_bacs_accounts');
	
	// Get sortcode label in the $locale array and use appropriate one
	$sort_code_label = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'woocommerce' );
	
	?>
    <section class="woocommerce-bacs-bank-details">
    	<h2 class="wc-bacs-bank-details-heading"><?php _e( '입금 계좌정보', 'twentynineteen-child' ); ?></h2>
		<p class="wc-bacs-bank-details-description"><?php _e( '아래 계좌로 입금하시면 결제가 완료됩니다.', 'twentynineteen-child' ); ?></p>
    <?php
    $i = -1;
    if ( $bacs_info ) : foreach ( $bacs_info as $account ) :
    $i++;

    $account_name   = esc_attr( wp_unslash( $account['account_name'] ) );
    $bank_name      = esc_attr( wp_unslash( $account['bank_name'] ) );
    $account_number = esc_attr( $account['account_number'] );
    $sort_code      = esc_attr( $account['sort_code'] );
    $iban_code      = esc_attr( $account['iban'] );
    $bic_code       = esc_attr( $account['bic'] );
    
    $order_id = get_query_var('view-order');
    $order = new WC_Order($order_id);
    $bacs_date = strtotime('+3days', strtotime($order->get_date_created()) );
    $bacs_date_format = date( __( 'Y년 m월 d일', 'twentynineteen-child' ), $bacs_date);
    ?>
	    <ul class="wc-bacs-bank-details order_details bacs_details">
	        <li class="bank_name"><?php _e('은행명', 'twentynineteen-child'); ?> <strong><?php echo $bank_name; ?></strong></li>
	        <li class="account_number"><?php _e('계좌번호', 'twentynineteen-child'); ?> <strong><?php echo $account_number; ?></strong></li>
	        <li class="account_name"><?php _e('예금주', 'twentynineteen-child'); ?> <strong><?php echo $account_name; ?></strong></li>
	        <li class="account_bci"><?php _e('Swift Code', 'twentynineteen-child'); ?> <strong><?php echo $bic_code;?></strong></li>
	    <?php if(is_account_page()):?>
	        <li class="account_name"><?php _e('후원자명', 'twentynineteen-child'); ?> <strong><?php echo $order->get_billing_first_name();?></strong></li>
	        <li class="account_name"><?php _e('입금금액', 'twentynineteen-child'); ?> <strong><?php echo wc_price($order->get_total());?></strong></li>
	        <li class="account_name"><?php _e('입금기한', 'twentynineteen-child'); ?> <strong><?php echo $bacs_date_format;?></strong></li>
	    <?php endif;?>
	    </ul>
    <?php endforeach; endif; ?>
	    <div class="basc-caution">
		    <h2 class="wc-bacs-bank-caution-heading"><?php _e( '무통장 입금시 유의사항', 'twentynineteen-child' ); ?></h2>
		    <ul class="wc-bacs-bank-caution">
		        <li><?php _e('입금시, 후원자명과 입금자명이 일치되어야 입금확인이 가능합니다.', 'twentynineteen-child'); ?></li>
		        <li><?php _e('입금기한 경과 후에는 자동 취소처리 됩니다.', 'twentynineteen-child'); ?></li>
		    </ul>
	    </div>
    </section>
    <?php
    $output = ob_get_clean();

    if ( $echo )
        echo $output;
    else
        return $output;
}

//결제수단 별 메세지
function add_content_thankyou_eximbay_card() {
	$eximbay_thankyou_text = '<div class="eximbay-card-info"><ol>
								<li>'.__('본 결제 수단은 Eximbay를 통해 제공 되고있으며, <a href="https://www.eximbay.com">www.eximbay.com</a> 으로 청구 됩니다', 'twentynineteen-child').'</li>'.
								'<li>'.__('주의: 청구서명은 <a href="https://www.eximbay.com">EXIMBAY.COM</a> 으로 기재 되오니 참고 바랍니다.', 'twentynineteen-child').'</li>
							</ol></div>';
	echo $eximbay_thankyou_text;
}
add_action( 'woocommerce_thankyou_eximbay_card', 'add_content_thankyou_eximbay_card' );
//무통장입금 안내문
function add_content_thankyou_bacs() {
	get_bacs_account_details_html();
}
add_action( 'woocommerce_account_transfer', 'add_content_thankyou_bacs' );

function add_content_order_eximbay_card() {
	if(is_account_page()):
		$order_id = get_query_var('view-order');
		$order = new WC_Order($order_id);
		if($payment_method = $order->get_payment_method()):
?>
<?php if($payment_method == 'bacs' && $order->get_status() === 'on-hold'): ?>
	<?php get_bacs_account_details_html();?>
<?php elseif($payment_method == 'eximbay_card'): ?>
<div class="eximbay-card-info">
	<ol>
		<li><?php _e('본 결제 수단은 Eximbay를 통해 제공 되고있으며, <a href="https://www.eximbay.com">www.eximbay.com</a> 으로 청구 됩니다', 'twentynineteen-child'); ?></li>
		<li><?php _e('주의: 청구서명은 <a href="https://www.eximbay.com">EXIMBAY.COM</a> 으로 기재 되오니 참고 바랍니다.', 'twentynineteen-child'); ?></li>
	</ol>
</div>
<?php endif;?>
<?php
		endif;
	endif; //is_account_page
}
add_action( 'woocommerce_order_details_after_order_table', 'add_content_order_eximbay_card', 2 );

/*add_action( 'woocommerce_view_order', 'display_bacs_account_details_on_view_order', 5, 1 );
function display_bacs_account_details_on_view_order( $order_id ){
	// Get an instance of the WC_Order object
	$order = wc_get_order( $order_id );
	
	if( $order->get_payment_method() === 'bacs' && $order->get_status() === 'on-hold' ){
		get_bacs_account_details_html();
	}
}*/

//무통장입금 필드수정
add_filter('woocommerce_bacs_account_fields','custom_bacs_fields');
function custom_bacs_fields() {
	global $wpdb;
	$account_details = get_option( 'woocommerce_bacs_accounts', array(
					array(
							'account_name'   => get_option( 'account_name' ),
							'account_number' => get_option( 'account_number' ),
							'sort_code'      => get_option( 'sort_code' ),
							'bank_name'      => get_option( 'bank_name' ),
							'iban'           => get_option( 'iban' ),
							'bic'            => get_option( 'bic' )
					)
				)
			);
	
	
	$account_fields = array(
			'bank_name'      => array(
					'label' => __('은행명', 'twentynineteen-child'),
					'value' => $account_details[0]['bank_name']
			),
			'account_number' => array(
					'label' => __( '계좌번호', 'twentynineteen-child' ),
					'value' => $account_details[0]['sort_code'].' '.$account_details[0]['account_number']
			),
			'account_name'   => array(
					'label' => __('예금주', 'twentynineteen-child'),
					'value' => $account_details[0]['account_name']
			),
			'bic'            => array(
					'label' => __( 'BIC', 'woocommerce' ),
					'value' => $account_details[0]['bic']
			)
	);
	
	return $account_fields;
}