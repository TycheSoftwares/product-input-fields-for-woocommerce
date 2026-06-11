/**
 * Product Input Fields - Blocks & Express Checkout JS
 *
 * @version 1.1.0
 */

(function ($) {
    'use strict';
    if (typeof algPifBlocksConfig === 'undefined') {
       return;
    }
    var PIF = {
        config: algPifBlocksConfig,
        sessionSaved: false,
        fetchPatched: false,
        init: function () {
            if (!this.config.allFields || this.config.allFields.length === 0) {
                return;
            }
            this.patchFetch();
            this.interceptClicks();
        },

        /**
         * Patch window.fetch to intercept WooCommerce Store API add-item calls.
         * WooCommerce Blocks uses fetch() — $_POST is always empty without this.
         */
        patchFetch: function () {
            if (this.fetchPatched) {
                return;
            }
            this.fetchPatched = true;
            var self          = this;
            var originalFetch = window.fetch;

            window.fetch = function (url, options) {
                var urlStr = (typeof url === 'string') ? url
                    : (url && url.url ? url.url : String(url));
                if (urlStr.indexOf('/wc/store') !== -1 && urlStr.indexOf('add-item') !== -1) {
                    // Validate required fields.
                    if (!self.validate()) {
                        return Promise.reject(new Error('PIF required fields missing.'));
                    }
                    var capturedUrl     = url;
                    var capturedOptions = options;

                    return self.saveToSessionAsync().then(function () {
                        return originalFetch.call(window, capturedUrl, capturedOptions);
                    }).catch(function (err) {
                        return originalFetch.call(window, capturedUrl, capturedOptions);
                    });
                }
                return originalFetch.call(window, url, options);
            };
        },

        /**
         * Fallback click intercept for classic add-to-cart and express checkout buttons.
         * Uses capture phase so it fires before any other handler.
         */
        interceptClicks: function () {
            var self = this;

            document.addEventListener('click', function (e) {
                var t      = e.target;
                var isCart = (
                    t.classList.contains('single_add_to_cart_button') ||
                    t.classList.contains('add_to_cart_button') ||
                    (t.type === 'submit' && $(t).closest('form.cart').length) ||
                    $(t).closest('.wp-block-add-to-cart-form').length
                );
                if (!isCart) return;
                if (self.sessionSaved) {
                    return;
                }

                if (!self.validate()) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
                self.saveToSessionXhr();
            }, true);

        },

        /**
         * Validate all required PIF fields. Shows inline error if any empty.
         * @return {boolean}
         */
        validate: function () {
            var required = this.config.requiredFields;
            if (!required || required.length === 0) {
                return true;
            }

            for (var i = 0; i < required.length; i++) {
                var f = required[i];

                // Skip if the field element is not present on the page
                // (e.g. position set to "Do not display").
                var fieldEl = document.querySelector(
                    '[name="' + f.fieldName + '"], [name="' + f.fieldName + '[]"]'
                );
                if (!fieldEl) {
                    continue;
                }

                var val = this.getFieldValue(f.fieldName, f.type);
                if (!val || val.toString().trim() === '') {
                    var msg = this.config.messages.required;
                    if (f.title) msg += ': ' + f.title;
                    this.showError(msg);
                    return false;
                }
            }
            return true;
        },

        /**
         * Get the current value of a single PIF field.
         * Special handling for checkbox (hidden input collision) and file fields.
         */
        getFieldValue: function (fieldName, type) {
            if (type === 'file') {
                var el = document.querySelector('[name="' + fieldName + '"]');
                var v  = (el && el.files && el.files.length) ? el.files[0].name : '';
                return v;
            }

            if (type === 'checkbox') {
                var cb = document.querySelector('input[type="checkbox"][name="' + fieldName + '"]');
                var cv = (cb && cb.checked) ? 'yes' : '';
                return cv;
            }

            var els  = document.querySelectorAll('[name="' + fieldName + '"], [name="' + fieldName + '[]"]');
            if (!els.length) return '';

            var vals = [];
            els.forEach(function (el) {
                if ((el.type === 'radio' || el.type === 'checkbox') && !el.checked) return;
                if (el.value) vals.push(el.value);
            });
            var result = vals.join('');
            return result;
        },

        /**
         * Collect current values of ALL enabled PIF fields from the product page.
         * Also records which fields are actually rendered in the DOM so PHP can
         * skip required-field validation for fields that were never displayed.
         * @return {{ values: Object, files: Object, rendered: Array }}
         */
        collectValues: function () {
            var values   = {};
            var files    = {};
            var rendered = [];
            (this.config.allFields || []).forEach(function (f) {
                var name = f.fieldName;
                if (f.type === 'file') {
                    var el = document.querySelector('[name="' + name + '"]');
                    if (el) {
                        rendered.push(name);
                        files[name] = (el.files && el.files.length) ? el.files[0].name : '';
                    }
                } else if (f.type === 'checkbox') {
                    var cb = document.querySelector('input[type="checkbox"][name="' + name + '"]');
                    if (cb) {
                        rendered.push(name);
                        values[name] = cb.checked ? 'yes' : 'no';
                    }
                } else if (f.type === 'color') {
                    var colorInput = document.querySelector('input[type="color"][name="' + name + '"]')
                        || document.querySelector('input[type="text"][name="' + name + '"]')
                        || document.querySelector('input[name="' + name + '"]');
                    if (colorInput) {
                        rendered.push(name);
                        var colorVal = colorInput.value || '';
                        if (!colorVal) {
                            var spPreview = document.querySelector('.sp-preview-inner');
                            if (spPreview && spPreview.style.backgroundColor) {
                                colorVal = spPreview.style.backgroundColor;
                            }
                        }
                        values[name] = colorVal;
                    }
                } else {
                    var input = document.querySelector('[name="' + name + '"]');
                    if (!input) {
                        var inputs = document.querySelectorAll('[name="' + name + '[]"]');
                        if (inputs.length) {
                            rendered.push(name);
                            var arr = [];
                            inputs.forEach(function (el) {
                                if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
                                if (el.value) arr.push(el.value);
                            });
                            values[name] = arr.length ? arr : '';
                        }
                    } else {
                        rendered.push(name);
                        values[name] = input.value || '';
                    }
                }
            });
            return { values: values, files: files, rendered: rendered };
        },

        /**
         * Save field values to WC session via synchronous XHR inside a Promise.
         * Used by fetch intercept — must complete before original fetch fires.
         * @return {Promise<void>}
         */
        saveToSessionAsync: function () {
            var data   = this.collectValues();
            var self   = this;
            var config = this.config;

            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', config.ajaxUrl, false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                var params = self.buildParams({
                    action:       'alg_wc_pif_save_for_express',
                    nonce:        config.nonce,
                    product_id:   config.productId,
                    pif_values:   data.values,
                    pif_files:    data.files,
                    pif_rendered: data.rendered,
                });

                xhr.onload = function () {
                    self.sessionSaved = true;
                    setTimeout(function () {
                        self.sessionSaved = false;
                    }, 5000);
                    resolve();
                };
                xhr.onerror = function () {
                    reject(new Error('XHR failed'));
                };
                xhr.send(params);
            });
        },

        /**
         * Save field values to WC session via synchronous XHR (fire-and-forget).
         */
        saveToSessionXhr: function () {
            var data   = this.collectValues();
            var config = this.config;
            var self   = this;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', config.ajaxUrl, false);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var params = this.buildParams({
                action:       'alg_wc_pif_save_for_express',
                nonce:        config.nonce,
                product_id:   config.productId,
                pif_values:   data.values,
                pif_files:    data.files,
                pif_rendered: data.rendered,
            });

            xhr.send(params);

            self.sessionSaved = true;
            setTimeout(function () {
                self.sessionSaved = false;
            }, 5000);
        },

        /**
         * Recursively serialize a nested object to URL-encoded form params.
         */
        buildParams: function (obj, prefix) {
            var parts = [];
            for (var key in obj) {
                if (!obj.hasOwnProperty(key)) continue;
                var fullKey = prefix ? prefix + '[' + key + ']' : key;
                var val     = obj[key];

                if (val !== null && typeof val === 'object' && !Array.isArray(val)) {
                    parts.push(this.buildParams(val, fullKey));
                } else if (Array.isArray(val)) {
                    val.forEach(function (v) {
                        parts.push(encodeURIComponent(fullKey + '[]') + '=' + encodeURIComponent(v));
                    });
                } else {
                    parts.push(
                        encodeURIComponent(fullKey) + '=' +
                        encodeURIComponent(val === null || val === undefined ? '' : val)
                    );
                }
            }
            return parts.join('&');
        },

        /**
         * Display inline error notice above PIF field table or add-to-cart form.
         */
        showError: function (msg) {
            var old = document.querySelector('.alg-pif-blocks-error');
            if (old) old.parentNode.removeChild(old);
            var notice           = document.createElement('div');
            notice.className     = 'woocommerce-error alg-pif-blocks-error';
            notice.style.cssText = 'margin:10px 0;padding:10px;border:1px solid red;color:red;background:#fff0f0;';
            notice.textContent   = msg;

            var table  = document.querySelector('.alg-product-input-fields-table');
            var form   = document.querySelector('form.cart');
            var anchor = table || form;
            if (anchor && anchor.parentNode) {
                anchor.parentNode.insertBefore(notice, anchor);
            } else {
                document.body.prepend(notice);
            }
            notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            PIF.init();
        });
    } else {
        PIF.init();
    }
})(jQuery);