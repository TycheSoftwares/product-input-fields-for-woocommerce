/**
 * alg-wc-product-input-fields.
 *
 * @version 1.1.6
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

    //Allows color typing
    var pif_ct = {
        init: function () {
            if (jQuery('.alg-pif-color-text-input').length) {
                this.sync_input_value('input[type="color"]', '.alg-pif-color-text-input', 'input');
                this.sync_input_value('.alg-pif-color-text-input', 'input[type="color"]', 'change');
            }
        },
        sync_input_value: function (input_selector_1, input_selector_2, event) {
            var colorPickers = document.querySelectorAll(input_selector_1);
            [].forEach.call(colorPickers, function (div) {
                div.addEventListener(event, function (e) {
                    div.parentNode.querySelector(input_selector_2).value = e.target.value;
                }, false);
            });
        }
    }
    pif_ct.init();
});