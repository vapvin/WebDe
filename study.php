
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