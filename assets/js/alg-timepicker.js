/**
 * alg-timepicker.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @package Input Fields for WooCommerce Pro
 */
jQuery(document).ready(
	function () {
		jQuery("input[display='time']").each(
			function () {
				jQuery(this).timepicker(
					{
						timeFormat: jQuery(this).data("timeformat"),
						interval: jQuery(this).attr("interval"),
						change: function(time) {
							jQuery(this).trigger('change');
						}
					}
				);
			}
		);
	}
);

// Add the CSS rules For Divi theme.
var style = document.createElement('style');
style.innerHTML = '.ui-timepicker-container.ui-timepicker-no-scrollbar.ui-timepicker-standard { z-index: 9999 !important; }';
document.head.appendChild(style);