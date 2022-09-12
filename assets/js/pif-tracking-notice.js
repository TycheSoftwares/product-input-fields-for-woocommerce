/**
 * Data Tracking notice.
 *
 * @namespace product_input_fields
 * @since 1.3.3
 */
// Tracking Notice dismissed.
jQuery(document).ready( function() {
	
	jQuery( '.pif-lite-tracker' ).on( 'click', '.notice-dismiss', function() {

		var data = {
			admin_choice: 'dismissed',
			action: 'pif_lite_admin_choice'
		};

		jQuery.post( pif_dismiss_params.ajax_url, data, function() {
		});
	});

});