/**
 * alg-datepicker.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @package Input Fields for WooCommerce Pro
 */

jQuery(document).ready(
	function () {
		jQuery("input[display='date']").each(
			function () {
				jQuery(this).datepicker(
					{
						dateFormat: jQuery(this).attr("dateformat"),
						minDate: jQuery(this).attr("mindate"),
						maxDate: jQuery(this).attr("maxdate"),
						firstDay: jQuery(this).attr("firstday"),
						changeYear: jQuery(this).attr("changeyear"),
						yearRange: jQuery(this).attr("yearrange")
					}
				);
			}
		);
	}
);

jQuery( document ).ajaxComplete(function( event,request, settings ) {
	var text = settings.data;
	var term = "action=flatsome_quickview";
	if( text.indexOf( term ) != -1 ) {
		jQuery("input[display='date']").each(
			function () {
				jQuery(this).datepicker(
					{
						dateFormat: jQuery(this).attr("dateformat"),
						minDate: jQuery(this).attr("mindate"),
						maxDate: jQuery(this).attr("maxdate"),
						firstDay: jQuery(this).attr("firstday"),
						changeYear: jQuery(this).attr("changeyear"),
						yearRange: jQuery(this).attr("yearrange")
					}
				);
			}
		);
	}
});
