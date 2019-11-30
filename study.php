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