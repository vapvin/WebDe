<?php

/**체크아웃**/
//체크아웃필드 추가삭제
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
function custom_override_checkout_fields($fields)
{
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
        'class' => array('form-row-wide'),
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
        'class' => array('form-row-wide'),
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
        'class' => array('form-row-wide'),
        'priority' => 38,
    );

    return $fields;
}
