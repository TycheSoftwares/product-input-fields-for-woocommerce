=== Product Input Fields for WooCommerce Pro ===
Contributors: tychesoftwares
Tags: woocommerce, product input fields
Requires at least: 4.4
Requires PHP: 7.4
Tested up to: 6.9.4
Stable tag: 3.1.0
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

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Product Input Fields".

== Screenshots ==

1. Frontend options.
2. Email options.
3. Setting number of global (i.e. for all products) product input fields.
4. Setting global (i.e. for all products) product input field options.
5. Setting local (i.e. on per product basis) product input field options.

== Changelog ==

= 3.1.0 - 31/03/2026 =
* Fix - Validation triggered on “Add to Cart” even when the field was disabled.
* Fix - Field price was displayed even when the field was hidden by conditional logic.
* Fix - Field price showed as 0 when pricing was disabled after migrating to v3.0.0.
* Fix - Shortcode [alg_display_product_input_fields] stopped working after migration to v3.0.0.
* Fix - Subtotal on product page was not updating correctly based on variation prices.

= 3.0.1 - 26/03/2026 =
* Fix - License activation data was not migrated correctly after updating to 3.0.0

= 3.0.0 - 26/03/2026 =
* New - Completely redesigned admin interface for better usability.
* Fix - When a Checkbox field was marked as required and conditional logic was applied, the validation was triggered on add to cart for products where the field is not displayed.

= 2.17.0 - 09/12/2025 =
* Fix - Deleting images from Radio/Multi-check options was not working properly.
* Fix - Decimal values were not accepted in the Price/Percentage field when adding pricing to multiple selectable options.
* Fix - On the product page frontend, checkbox and radio option images were misaligned or overlapping when images of different sizes were uploaded.

= 2.16.0 - 01/10/2025 =
* Fix - In admin settings, images for Select/Multiselect/Radio options were not uploading properly.
* Fix - Field width inconsistency when another field is hidden using conditional logic on the product page.
* Fix - Migration of previously added Select options from Lite version now works correctly after activating the Pro version.
* Fix - Conditional fee not being removed when switching to options without an associated fee on the product page.

= 2.15.0 - 22/07/2025 =
* Enhancement - Implemented conditional logic for field visibility. Fields can now be shown or hidden based on selected products, product categories, product tags, product variations, or the values of other fields.
* Fix - Resolved fatal error on the Edit Product page.
* Fix - Resolved debug log errors for improved plugin stability.
* Dev - Refactored plugin folder structure to enhance maintainability and simplify future development.

= 2.14.0 - 29/04/2025 =
* Fix - Incorrect price calculation for radio-type input fields on the product page when the Twenty Twenty Five theme is active.
* Fix - UI/UX improvements in the backend for ease of use and to improve the overall experience.
* Tweak - Updated for compatibility with WordPress 6.8.0
* Tweak - Updated for compatibility with WooCommerce 9.8.2

= 2.13.0 - 14/01/2025 =
* Fix - The required field validation message was appearing for fields that were excluded by product category while adding the product to cart.
* Fix - Thumbnail of the image uploaded with the File Upload field was not appearing on the order received page, order details page under my account, and in the order emails.
* Fix - Compatibility issue with Porto theme where adding a product to cart from the shop page was not redirecting to product page when there were mandatory input fields on the product.

= 2.12.0 - 27/11/2024 =
* Tweak - Added 'Skip and Deactivation' button to the deactivation popup.
* Fix - Translation loading was triggered too early, causing errors when changing the site language in WordPress 6.7.
* Fix - 'Required field' notice displayed on the product page even after entering a value for the product input field.

= 2.11.0 - 01/10/2024 =
* Fix - Resolved compatibility issues with the Divi theme.
* Fix - Resolved compatibility issues with the WooCommerce Print Invoices/Packing Lists plugin.
* Fix - The product input field's price was added to the total, but the actual product price was not being included in the total price shown on the product page.
* Fix - Clicking on the "Add Option" button while setting up the Select input type field was adding two options.
* Fix - Some strings in the plugin were not translation ready.

= 2.10.0 - 23/07/2024 =
* Tweak - Simplified the license activation process.
* Fix - The default setting value for the checkbox was not working.

= 2.9.1 - 28/05/2024 =
* Fix - Fixed Price set for the checkbox product input field is charged twice when the order is placed.
* Fix - Fixed Option to allow for the Price field in the "Enable Pricing Option" section to accept decimal/float numbers. 
* Fix - Fixed When a product without a paid option is added to the cart, subsequent products no longer consider the paid options.
* Fix - Fixed Input field values passed to the cart.
* Fix - Fixed some deprecated notices in PHP version 8.3.

= 2.9.0 - 03/04/2024 =
* Tweak - Update compatibility with WordPress 6.5.
* Tweak - Update compatibility with WooCommerce 8.7.
* Fix - Cross Site Request Forgery (CSRF) vulnerability.
* Fix - Fixed Compatibility issues with WooCommerce Print Invoices/Packing Lists plugin.
* Fix - Fixed warning displayed while using Textarea input field on cart and checkout Page.

= 2.8.0 - 06/02/2024 =
* FIX - Compatibility with Booking & Appointment Plugin for WooCommerce when the prices of products in the cart to be doubled when both plugins activate.
* FIX - Conflict with custom themes based on ACF pro.
* FIX - PHP Warning showing in the log file.
* FIX - Input field value does not appear on the Cart and Checkout block page.

= 2.7.0 - 31/10/2023 =
* FIX - Wrong text appear when Date picker/ Weekpicker input field selected.
* FIX - The end date is repeated when we click again in the weekday input field.
* FIX - Comapbility with Booking & Appointment Plugin for WooCommerce when price option disable in Product Input Fields for Woocommerce.
* FIX - The date and month are repeated on product page with the weekday input field when formate is 'dd/yy/mm'.
* FIX - Input fields value display twice on the cart page with Woocommerce 7.8.0 or above.
* FIX - "Exclude by Categories" option and it's validation doesn't work when product has additional category.
* FIX - Display Wrong calculation on cart page when decimal operator is comma(,)
* FIX - Compability with YITH WooCommerce Product Bundles Premium.
* FIX - Text input field data not showing on order emails & backend order page.

= 2.6.0 - 13/06/2023 =
* Enhancement - Preview thumbnails for uploaded files on Cart, Checkout, Thank you, Email, and Edit order pages.
* Fix - File Upload field not working due to Astra pro plugin compatibility.
* Fix - Variable subscription product prices in cart was coming incorrect due to compitabiltiy issue with WooCommerce Subscriptions plugin.
* Fix - File upload input type for bundle product was not working properly.
* Fix - Throwing fatal error due to data type issue when WooCommerce Product Add-ons plugin was being used with this plugin.
* Fix - Mini cart UI was getting broke due to compatibility issues with the LeadEngine WordPress theme.
* Fix - Throwing fatal error on adding the products to cart when the plugin was active.
* Fix - Datepicker input field was not working due to conflict with Elementor page builder.

= 2.5.0 - 25/10/2022 =
* Enhancement - Introduced compatibility with WooCommerce High Performance Order Storage.

= 2.4.2 - 27/09/2022 =
* Enhancement - Fill input field values from URL parameter.
* Fix - Cart page throws pricing error when 'Input Field Pricing Options' is enabled.
* Fix - The Max length limit field is not displaying for Text & Textarea input field types.
* Fix - Improper handling of quotes results in bugs on the frontend and cross site scripting vulnerability.
* Fix - Some unwanted words are being displayed on the Single Product page.

= 2.4.1 - 05/07/2022 =
* Fix: Input type file was showing array instead of the file name on cart and checkout page.

= 2.4.0 - 06/06/2022 =
* Fix: When using checkbox/radio/multicheck field, blank box is added on the frontend if option name value is left empty from backend.
* Fix: The tooltip is missing "Exclude by Category/Include by Category" on the edit product page.
* Fix: Word "Category" should be replaced with "Categories".
* Fix: Removed uppercase field for checkbox type field.
* Enhancement: Added optoin to set images for each checkbox/radio/select/multicheck options.
* Enhancement: Added option to set the input field based on the product category for global fields.
* Enhancement: Added option to re-order input field on product page on single product level.
* Enhancement: Added conditional fields option to display field depending on quantity of each product.

= 2.3.1 - 27/04/2022 =
* Fix: Notice in debug log.
* Fix: Input field price not working due to Flatsome theme query

= 2.3.0 - 26/04/2022 =
* Enhancement: Added option to exclude Global product input field by category.
* Enhancement: Option to allow for the Price field in the "Enable Pricing Option" section to accept decimal/float numbers.
* Enhancement: Removed Uppercase setting for Date/Time picker and select field.
* Fix: Notice in debug log.
* Fix: Subtotal not working when Flatsome theme is installed.
* Fix: Calculated percentage value instead of percentage on single product page.
* Fix: Notice to activate license.
* Fix: Showing unnecessary notice.
* Fix: Compatibility with Multi-Currency plugin.
* Fix: Unwanted fields in Global PIF setting page.
* Fix: Currency symbol position is not set according to woocommerce settings.
* Fix: Language of condition dropdown option.
* Fix: No 'Percentage' title if the % condition is set.
* Fix: Price not getting displayed when select field option is set on front end.

= 2.2.0 - 16/11/2021 =
* Enhancement: Added an option to convert the first letter to uppercase for the different input types fields.
* Enhancement: Added maximum length option for the textarea input type.
* Fix: Compatibility with the YITH request a quote plugin, as on the cart page error was coming due to which pricing functionality was disturbed.
* Fix: Special characters were getting converted into emojis after passing the data to the cart. This is fixed now.
* Fix: Pricing table was coming on the product page whenever the placeholder was set for the 'Select' input field type. This is fixed now.

= 2.1.2 - 07/10/2021 =
* Fix: Due to the change made in the last release decimal pricing on the cart page were not coming correctly. This is fixed now.
* Fix: Compatibility with the Porto theme.

= 2.1.1 - 21/09/2021 =

* Enhancement: Added a filter 'alg_wc_pif_no_pif_in_child_products' for not showing the product input fields in the child products of composite products on the cart page.
* Fix: Compatibility with the WooCommerce Bookings plugin.
* Fix: Compatibility with YITH WooCommerce Request a Quote plugin.
* Fix: Compatibilty with Themify Ultra theme.
* Fix: Compatibility with Divi theme.
* Fix: Checkbox was not coming on the product page when multi checkbox type is selected. This is fixed now.
* Fix: When there is a custom range picker on the product page from some other plugin, classes from our plugin were getting added in that. This is fixed now.
* Fix: The placeholder/default value was not getting used for 'Select' option type. This is fixed now.
* Fix: Fatal error was coming on the product frontend when plugin settings were disabled and shortcode is added. This is fixed now.

= 2.1.0 - 18/03/2021 =

* Fix: Compatibility with the Flatsome theme.
* Fix: Compatibility with the Booking & Appointment Plugin for WooCommerce.
* Fix: Compatibility with the WooCommerce Product Add-ons.
* Fix: Sometimes after selecting the variations on the product page, it keeps on loading and nothing happens. This is fixed now.
* Fix: Plugin was not getting activated with the PHP vesrion below 7.1.0 due to the syntax error. This is fixed now.
* Fix: Sometimes error was coming on some sites due to the way of fetching quantity selected in JS. This is fixed now.
* Fix: Pricing table was showing the incorrect values when two fields of same type are enabled and only 1 field have pricing option enabled. This is fixed now.
* Fix: In the variable product, when we change the variations than the prices of variation were not changing on product page. This is fixed now.
* Fix: When the global fields and local field both are enabled together then in the cart the PIF prices were not added. This is fixed now.
* Fix: On edit product page when we enable the product input field there was error coming in the console. This is fixed now.
* Fix: When we select the checkbox type field, than in the cart the value for that field is not passed properly. This is fixed now.
* Fix: Checkbox Type Options were displayed on the backend even if the Radio input type was selected. This is fixed now.
* Fix: Amount was coming wrong in the pricing table when the decimal separator was set as comma. This is fixed now.

= 2.0.0 - 15/12/2020 =

* Enhancement - Pricing can be set for the input fields. Different prices could be set for each input field.
* Tweak - When selecting an input field in the settings, only the related options for that input will be displayed.
* Fix - Range input option did not display the selected value on the product page.
* Fix - When entering the URL in Hebrew language, the Hebrew got erased from the URL after submission.
* Fix - When the selected values of select input contained quotes in it , it was not displayed correctly in the email or order page.
* Fix - After clicking on Order again button on My Account page, an error of required fields was displayed & product was not added to cart.
* Fix - When the "Select" input field was used, the values got changed when we added the product to the cart.
* Fix - The field values were not displayed in the cart when using WPBakery page builder.
* Fix - The description added in the setting "HTML to add before the product input fields" was shown on all the products even if the All Product settings was disabled.
* Fix - Security changes when handling downloads in the plugin.

= 1.3.5 - 24/07/2020 =

* Fix - The plugin was conflicting with Creta theme. This is fixed now.
* Dev - Ability to track non-sensitive diagnostic plugin data with the site admin's consent.

= 1.3.4 - 12/03/2020 =

* This update to the plugin is for announcing the plugin compatibility with WooCommerce v4.0.0

= 1.3.3 - 22/02/2020 = 

* This update to the plugin is for announcing the plugin compatibility with WooCommerce v3.9.x

= 1.3.2 - 10/04/2019 = 

* Fix - Update notification was not coming for the plugin when updated from version 1.3 to 1.3.1. This is fixed now. 

= 1.3.1 - 10/04/2019 = 
* Enhancement - The plugin is made compatible with the WPML plugin. The static strings and Dynamic strings can now be translated into different languages from the plugin. 
* Enhancement - A new page is added which will be shown when the plugin is installed and activated for the first time. This page allows users to activate the license key  for future updates. 
* Fix - Data was not getting deleted from the database when the plugin is uninstalled. This is fixed now. 

= 1.3 = 
* This is minor update to the plugin. This update just has changes pointing to the new server for automatic updates. 

= 1.2.5 - 23/01/2019 =
* Dev - Plugin URI updated.
* Dev - Admin settings restyled and descriptions updated.
* Dev - Code clean up.

= 1.2.4 - 26/10/2018 =
* Add compatibility with Advanced Order Export For WooCommerce plugin

= 1.2.3 - 09/10/2018 =
* Display fields on PDF Invoices & Packing Slips plugin

= 1.2.2 - 19/09/2018 =
* Add 'Load Datepicker Style' option
* Add 'Load Timepicker Style' option
* Update Timepicker JS
* Turn off autocomplete on timepicker, datepicker and weekpicker
* Fix Datepicker, Timepicker and Weekpicker style

= 1.2.1 - 18/09/2018 =
* Add color input compatibility with Opera and Safari

= 1.2.0 - 17/09/2018 =
* Add color section on admin input fields options
* Add option to allow typing or pasting the color manually
* Update WC tested up to

= 1.1.9 - 10/08/2018 =
* Fix maxlength attribute on textarea

= 1.1.8 - 01/08/2018 =
* Fix PHP warnings
* Fix file uploading when using multiple file inputs
* Add "Smart Textarea" option, showing only the textarea excerpt on frontend and hovering it will make it display the full content

= 1.1.7 - 12/07/2018 =
* Add multi select option for select field
* Add multi checkbox option
* Check if order and input fields exist before trying to delete file uploads
* Improve help link for pattern attribute
* Improve input sanitizing
* Display multiple array value as comma separated string

= 1.1.6 - 18/05/2018 =
* Remove slashes from the values

= 1.1.5 - 02/05/2018 =
* Remove check for pro version

= 1.1.4 - 29/04/2018 =
* Fix empty setting section on admin settings

= 1.1.3 - 28/04/2018 =
* Add composer
* Add new option to convert characters to uppercase version, when possible

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

= 1.2.4 =
* Add compatibility with Advanced Order Export For WooCommerce plugin
