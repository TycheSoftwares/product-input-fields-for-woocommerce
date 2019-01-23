=== Product Input Fields for WooCommerce ===
Contributors: algoritmika, anbinder, karzin
Tags: woocommerce, product input fields
Requires at least: 4.4
Tested up to: 5.0
Stable tag: 1.2.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add custom frontend input fields to WooCommerce products.

== Description ==

**Product Input Fields for WooCommerce** plugin lets you add custom input fields to WooCommerce product's frontend for customer to fill before adding product to cart.

Input fields can be added **globally** (i.e. for all products) or on **per product** basis.

You can choose numerous different **types** for fields:

* Text
* Textarea
* Number
* Checkbox
* Color
* File
* Datepicker
* Weekpicker
* Timepicker
* Select
* Radio
* Password
* Country
* Email
* Phone
* Search
* URL
* Range

Each type comes with specific **options** you can set for each field.

Additionally you can set fields **HTML template** and much more.

Plugin is limited to adding two input fields to each product - one (global) field for all products and one more (local) for each product individually. If you wish to add unlimited number of global and/or local product input fields, please check our [Product Input Fields for WooCommerce Pro](https://wpcodefactory.com/item/product-input-fields-woocommerce/) plugin.

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!

== Installation ==

1. Upload the entire 'product-input-fields-for-woocommerce' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Start by visiting plugin settings at WooCommerce > Settings > Product Input Fields.

== Screenshots ==

1. Frontend options.
2. Email options.
3. Setting number of global (i.e. for all products) product input fields.
4. Setting global (i.e. for all products) product input field options.
5. Setting local (i.e. on per product basis) product input field options.

== Changelog ==

= 1.2.1 - 23/01/2019 =
-* Dev - Plugin URI updated.

= 1.2.0 - 25/10/2018 =
* Add compatibility with Advanced Order Export For WooCommerce plugin

= 1.1.9 - 09/10/2018 =
* Display fields on PDF Invoices & Packing Slips plugin

= 1.1.8 - 19/09/2018 =
* Add 'Load Datepicker Style' option
* Add 'Load Timepicker Style' option
* Update Timepicker JS
* Turn off autocomplete on timepicker, datepicker and weekpicker
* Fix Datepicker, Timepicker and Weekpicker style

= 1.1.7 - 18/09/2018 =
* Add color input compatibility with Opera and Safari

= 1.1.6 - 17/09/2018 =
* Add color section on admin input fields options
* Add option to allow typing or pasting the color manually
* Update WC tested up to

= 1.1.5 - 10/08/2018 =
* Fix maxlength attribute on textarea

= 1.1.4 - 01/08/2018 =
* Check if order and input fields exist before trying to delete file uploads
* Improve help link for pattern attribute
* Improve input sanitizing
* Display multiple array value as comma separated string
* Fix PHP warnings
* Fix file uploading when using multiple file inputs
* Add "Smart Textarea" option, showing only the textarea excerpt on frontend and hovering it will make it display the full content
* Add 'Textarea Auto Height' option, making the textarea auto increase its height as users type

= 1.1.3 - 18/05/2018 =
* Add composer
* Add filter 'alg_product_input_fields_options' allowing changes on field options
* Add filter 'alg_wc_pif_field_html' allowing changes the field html
* Remove check for pro version
* Remove slashes from the values

= 1.1.2 - 18/04/2018 =
* Dev - "WC tested up to" added to plugin header.

= 1.1.1 - 30/10/2017 =
* Dev - WooCommerce v3.2 compatibility - Admin settings - `select` type options fixed.
* Dev - WooCommerce v3.0 compatibility - "woocommerce_add_order_item_meta hook uses out of date data structures and function is deprecated..." notice fixed.
* Fix - `add_product_input_fields_to_order_item_meta()` - Checking if product input fields values exist (fixes notice in log).
* Dev - Saving settings array as main class property.

= 1.1.0 - 15/06/2017 =
* Dev - WooCommerce 3.x.x compatibility - `output_custom_input_fields_in_admin_order()` - Using `meta_exists()` and `get_meta()` functions to access order items meta data.
* Dev - WooCommerce 3.x.x compatibility - `alg_get_frontend_product_input_fields()` - Product ID (using `get_id()` function instead of accessing `id` object property directly).
* Dev - Core - `add_files_to_email_attachments()` - Additional validation added.
* Tweak - Plugin link updated from <a href="https://coder.fm">https://coder.fm</a> to <a href="https://wpcodefactory.com">https://wpcodefactory.com</a>.

= 1.0.1 - 28/03/2017 =
* Dev - Language (POT) file added.
* Dev - readme.txt updated (screenshots added etc.).
* Tweak - http replaced with https in links to coder.fm.

= 1.0.0 - 28/03/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.2.0 =
* Add compatibility with Advanced Order Export For WooCommerce plugin