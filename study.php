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