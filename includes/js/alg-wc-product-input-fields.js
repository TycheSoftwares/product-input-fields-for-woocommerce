/**
 * alg-wc-product-input-fields.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
jQuery(document).ready(function() {
	jQuery('input,textarea').focus(function(){
	   jQuery(this).data('placeholder',jQuery(this).attr('placeholder'))
	   jQuery(this).attr('placeholder','');
	});
	jQuery('input,textarea').blur(function(){
	   jQuery(this).attr('placeholder',jQuery(this).data('placeholder'));
	});
});