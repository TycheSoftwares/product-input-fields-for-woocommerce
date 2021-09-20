/**
 * alg-wc-product-input-fields.
 *
 * @version 1.1.7
 * @since   1.0.0
 */
jQuery(document).ready(function () {
	jQuery('input,textarea').focus(function () {
		jQuery(this).data('placeholder', jQuery(this).attr('placeholder'))
		jQuery(this).attr('placeholder', '');
	});
	jQuery('input,textarea').blur(function () {
		jQuery(this).attr('placeholder', jQuery(this).data('placeholder'));
	});
	jQuery("input[type='range'][name^='alg_wc_pif_']").each((index, value) => {
		let id = value.id;

		jQuery(value).parent().css('position', 'relative');
		jQuery(value).before('<span class="alg_wc_pif_range_min">' + jQuery(value).attr('min') + '</span>');
		jQuery(value).after('<span class="alg_wc_pif_range_bubble" style="float:right;" id="' + id + '_bubble">' + jQuery(value).attr('value') + '</span>');
		jQuery(value).after('<span class="alg_wc_pif_range_max">' + jQuery(value).attr('max') + '</span>');

		if (jQuery("#" + id + "_bubble").css("visibility") == 'hidden') {
			jQuery(value).hover((event) => {
				jQuery("#" + id + "_bubble").css('visibility', 'visible'); 
			});

			jQuery(value).mouseleave((event) => {
				jQuery("#" + id + "_bubble").css('visibility', 'hidden'); 
			}); 
		}
		jQuery(value).mousemove((event) => {
			jQuery("#" + id + "_bubble").html(event.target.value); 
		}); 
	});
});

//Color typing and syncing
var pif_ct = {
	init: function () {
		if (jQuery('.alg-pif-color-text-input').length && !jQuery('.alg-pif-color-wrapper .sp-replacer').length) {
			this.sync_input_value('input[type="color"]', '.alg-pif-color-text-input', 'input');
			this.sync_input_value('.alg-pif-color-text-input', 'input[type="color"]', 'change');
		}
	},
	sync_input_value: function (input_selector_1, input_selector_2, event) {
		var element1 = document.querySelectorAll(input_selector_1);
		[].forEach.call(element1, function (div) {
			div.addEventListener(event, function (e) {
				div.parentNode.querySelector(input_selector_2).value = e.target.value;
			}, false);
		});
	},
}

//Color typing using spectrum, when browsers can't render type=["color"]
var pif_ct_spectrum = {
	init: function () {
		if (jQuery('.alg-pif-color-text-input').length && jQuery('.alg-pif-color-wrapper .sp-replacer').length) {
			this.sync_input_value_spectrum('input[type="color"]', '.alg-pif-color-text-input', 'change.spectrum');
			this.sync_input_value_spectrum('.alg-pif-color-text-input', 'input[type="color"]', 'change');
		}
	},
	sync_input_value_spectrum: function (input_selector_1, input_selector_2, event) {
		var elements1 = jQuery(input_selector_1);
		elements1.each(function (index) {
			jQuery(this).on(event, function (e, tinycolor) {
				if (event == 'change.spectrum') {
					jQuery(this).parent().find(input_selector_2).attr('value', tinycolor.toHexString())
				} else {
					jQuery(this).parent().find(input_selector_2).spectrum("set", jQuery(this).val());
				}
			});
		});
	}
}

jQuery(document).ready(function () {
	pif_ct.init();
	pif_ct_spectrum.init();
});
