<?php
/**
 * PIF Cart Class
 *
 * Handles the cart functionality for the PIF plugin, including adding custom fields to the cart and displaying them in the cart and checkout pages.
 *
 * @package PIF/Cart
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

class PIF_Order {

	/**
	 * Function save_values_in_item.
	 *
	 * @version 1.1.1
	 * @since   1.1.1
	 * @param mixed $item Item Details.
	 * @param mixed $cart_item_key Cart Item Key.
	 * @param mixed $values Values.
	 */
	public static function save_values_in_item( $item, $cart_item_key, $values ) {
		$pif_values         = ALG_WC_PIF_ID . '_values';
		$item['pif_values'] = $values;
	}

	/**
	 * Function add_product_input_fields_to_order_item_meta.
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 * @param number $item_id Item ID.
	 * @param object $item Order Item object.
	 */
	public static function add_product_input_fields_to_order_item_meta( $item_id, $item ) {
		$scopes  = array( 'global', 'local' );
		$values  = $item->get_meta( 'pif_values' );

		foreach ( $scopes as $scope ) {
			$_product_input_fields = array();

			$product_input_fields  = ( isset( $values[ ALG_WC_PIF_ID . '_' . $scope ] ) ? $values[ ALG_WC_PIF_ID . '_' . $scope ] : array() );
			
			foreach ( $product_input_fields as $product_input_field ) {
				if ( 'file' === $product_input_field['type'] && isset( $product_input_field['_value'] ) ) {
					$value    = $product_input_field['_value'];
					$tmp_name = $value['_tmp_name'];
					if ( '' !== $tmp_name ) {
						$name       = $item_id . '_' . $value['name'];
						$upload_dir = alg_get_uploads_dir( 'product_input_fields' );
						if ( ! file_exists( $upload_dir ) ) {
							mkdir( $upload_dir, 0755, true ); //phpcs:ignore
						}
						$upload_dir_and_name = $upload_dir . '/' . $name;
						$file_data           = file_get_contents( $tmp_name ); //phpcs:ignore
						file_put_contents( $upload_dir_and_name, $file_data ); //phpcs:ignore
						unlink( $tmp_name ); //phpcs:ignore
						$value['_tmp_name']            = addslashes( $upload_dir_and_name );
						$product_input_field['_value'] = $value;
					}
				}
				$_product_input_fields[] = $product_input_field;
			}
			wc_add_order_item_meta( $item_id, '_' . ALG_WC_PIF_ID . '_' . $scope, $_product_input_fields );

		}
	}

	/**
	 * 
	 * 
	 */
	public static function output_custom_input_fields_in_admin_order( $item_id, $item, $echos = true, $simple_text = false ) {
		$scopes = array( 'global', 'local' );
		$html   = '';

		foreach ( $scopes as $scope ) {
			if ( ! $item->meta_exists( '_' . ALG_WC_PIF_ID . '_' . $scope ) ) {
				continue;
			}

			$product_input_fields = ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] ) : $item->get_meta( '_' . ALG_WC_PIF_ID . '_' . $scope ) ); // phpcs:ignore

			foreach ( $product_input_fields as $product_input_field ) {
				$values = '';
				if ( ! isset( $product_input_field['title'] ) || ! isset( $product_input_field['_field_nr'] ) || ! isset( $product_input_field['_value'] ) || ! isset( $product_input_field['type'] ) ) {
					continue;
				}

				$title = $product_input_field['title'];
				if ( '' === $title ) {
					$title = __( 'Product Input Field', 'product-input-fields-for-woocommerce' ) . ' (' . $scope . ') #' . $product_input_field['_field_nr'];
				}
				$_value     = $product_input_field['_value'];
				$key        = '';
				$image_html = '';
				if ( '' !== $_value ) {
					if ( 'checkbox' === $product_input_field['type'] ) {
						$values = ( 'yes' === $_value ) ? $product_input_field['type_checkbox_yes'] : $product_input_field['type_checkbox_no'];
					} elseif ( 'file' === $product_input_field['type'] ) {
						$values = maybe_unserialize( $_value );

						$img_url    = '';
						$field_img  = '';
						$dimensions = apply_filters( 'alg_wc_pif_thumbnail_dimensions', 64, 64 );
						$dimensions = ( ! is_array( $dimensions ) ) ? array( 64, 64 ) : $dimensions;
						$height     = ( is_array( $dimensions ) && isset( $dimensions[0] ) ) ? $dimensions[0] : 64;
						$width      = ( is_array( $dimensions ) && isset( $dimensions[1] ) ) ? $dimensions[1] : 64;

						if ( 'file' === $product_input_field['type'] ) {
							if ( in_array( $values['type'], array( 'image/jpeg', 'image/jpg', 'image/png' ), true ) && isset( $values['_tmp_name'] ) && ! empty( $values['_tmp_name'] ) ) {
								$img_url = home_url() . '/wp-content/uploads/pif_temp/' . $values['name'];
							} else {
								$img_url = self::pif_get_image_field_url( $values['type'], $values['name'] );
							}
							$field_img = ( ! empty( $img_url ) ) ? '<a href="javascript: void(0)" class="alg_image_preview" style="display: inline-block !important;vertical-align: top;margin-right: 5px;"><img class="site-logo" src= "' . esc_html( $img_url ) . '" style="height: ' . esc_html( $height ) . 'px; width: ' . esc_html( $width ) . 'px;" /></a> ' : '';
						}

						$values = ( isset( $values['name'] ) && '' !== $values['name'] ) ? '<a href="' . add_query_arg( 'alg_wc_pif_download_file', $item_id . '_' . $values['name'] ) . '">' . $values['name'] . '</a>' : '';
					} else {
						$values = $_value;
					}
					if ( $simple_text ) {
						$html .= "\n" . $title . ': ' . $values;
					} else {
						$field_html = '<div class="wc-order-item-variation"><strong>' . $title . ':</strong> ' . $values . '</div>';
						if ( 'file' === $product_input_field['type'] ) {
							if ( isset( $product_input_field['file_show_thumbnail'] ) && 'yes' === $product_input_field['file_show_thumbnail'] ) {
								$field_html = '<div class="wc-order-item-variation">' . $values . '</div>';
							}
						}
						$html .= $field_html;
					}
				}
			}
		}

		if ( $echos ) {
			echo $html; // phpcs:ignore
		} else {
			return $html;
		}
	}

	public static function output_custom_input_fields_in_invoice_plugin( $type, $item ) {
		self::output_custom_input_fields_in_admin_order( $item['item_id'], $item['item'] );
	}

	/**
	 * Function add_files_to_email_attachments.
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 * @param mixed   $attachments Attachements.
	 * @param boolean $status Status.
	 * @param mixed   $order Order Details.
	 */
	public static function add_files_to_email_attachments( $attachments, $status, $order ) {
		$scopes = array( 'global', 'local' );

		if (
		( 'new_order' === $status && 'yes' === pif_get_option( 'attach_to_admin_new_order', 'yes' ) ) ||
		( 'customer_processing_order' === $status && 'yes' === pif_get_option( 'attach_to_customer_processing_order', 'yes' ) )
		) {
			foreach ( $order->get_items() as $item_key => $item ) {
				foreach ( $scopes as $scope ) {
					$product_input_fields = maybe_unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] );
					if ( ! is_array( $product_input_fields ) || empty( $product_input_fields ) ) {
						continue;
					}

					foreach ( $product_input_fields as $product_input_field ) {
						if (
						isset( $product_input_field['type'] ) &&
						'file' === $product_input_field['type'] &&
						isset( $product_input_field['_value'] )
						) {
							$_value = $product_input_field['_value'];
							$_value = maybe_unserialize( $_value );
							if ( isset( $_value['_tmp_name'] ) ) {
								$file_path     = $_value['_tmp_name'];
								$attachments[] = $file_path;
							}
						}
					}
				}
			}
		}
		return $attachments;
	}

	/**
	 * Setups Input Fields Columns on Advanced Order Export For WooCommerce plugin
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 *
	 * @param mixed                 $value Value.
	 * @param WC_Order              $order Order Object.
	 * @param WC_Order_Item_Product $item Item Object.
	 * @return string
	 */
	public static function setup_adv_order_export_plugin_column( $value, $order, $item ) {
		$value .= self::output_custom_input_fields_in_admin_order( $item->get_id(), $item, false, true );
		return $value;
	}

	/**
	 * Adds Input Fields Column on Advanced Order Export For WooCommerce plugin
	 *
	 * @see https://br.wordpress.org/plugins/woo-order-export-lite/
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 *
	 * @param mixed $fields Fields.
	 *
	 * @return mixed
	 */
	public static function add_input_fields_columns_to_adv_order_export_plugin( $fields ) {
		$fields['apif'] = array(
			'label'   => __( 'Input Fields', 'product-input-fields-for-woocommerce' ),
			'colname' => __( 'Input Fields', 'product-input-fields-for-woocommerce' ),
			'checked' => 1,
		);
		return $fields;
	}

	/**
	 * Adds input fields when Order Again is called
	 *
	 * @param array $cart_item_meta Cart Item Array.
	 * @param array $product        Products in the cart.
	 */
	public static function pif_order_again_cart_item_data( $cart_item_meta, $product ) {
		$pif_fields_global = wc_get_order_item_meta( $product->get_id(), '_alg_wc_pif_global' );
		$pif_fields_local  = wc_get_order_item_meta( $product->get_id(), '_alg_wc_pif_local' );
		remove_all_filters( 'woocommerce_add_to_cart_validation' );
		if ( $pif_fields_global ) {
			$cart_item_meta['alg_wc_pif_global'] = $pif_fields_global;
		}
		if ( $pif_fields_local ) {
			if ( isset( $cart_item_meta['alg_wc_pif_local'] ) ) {
				$cart_item_meta['alg_wc_pif_local'] = $pif_fields_local;
			}
		}
		return $cart_item_meta;
	}

	/**
	 * Function for adding input fields in WooCommerce Print Invoices/Packing Lists plugin.
	 *
	 * @param array  $item_meta  An array containing the item meta data.
	 * @param int    $item_id    ID of the item.
	 * @param array  $item       Item details.
	 * @param object $product    Product object.
	 *
	 * @return string The formatted item meta data.
	 */
	public static function add_input_fields_in_product_meta( $item_meta, $item_id, $item, $product ) {
		$processed_meta   = array();
		$item_meta_fields = apply_filters( 'wcdn_product_meta_data', $item['item_meta'], $item );
		// Process global PIF fields if they exist.
		if ( isset( $item_meta_fields['_alg_wc_pif_global'] ) && is_array( $item_meta_fields['_alg_wc_pif_global'] ) ) {
			foreach ( $item_meta_fields['_alg_wc_pif_global'] as $custom_field ) {
				$field_title = isset( $custom_field['title'] ) ? $custom_field['title'] : 'No Title';
				$field_value = isset( $custom_field['_value'] ) ? $custom_field['_value'] : 'No Value';
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value );
				}
				$processed_meta[] = $field_title . ': ' . $field_value;
			}
		}
		// Process local PIF fields if they exist.
		if ( isset( $item_meta_fields['_alg_wc_pif_local'] ) && is_array( $item_meta_fields['_alg_wc_pif_local'] ) ) {
			foreach ( $item_meta_fields['_alg_wc_pif_local'] as $custom_field ) {
				$field_title = isset( $custom_field['title'] ) ? $custom_field['title'] : 'No Title';
				$field_value = isset( $custom_field['_value'] ) ? $custom_field['_value'] : 'No Value';
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value );
				}
				$processed_meta[] = $field_title . ': ' . $field_value;
			}
		}
		// Optionally, include additional product meta fields if needed.
		foreach ( $item['item_meta'] as $meta_key => $meta_value ) {
			if ( ! in_array( $meta_key, array( '_alg_wc_pif_global', '_alg_wc_pif_local' ), true ) ) {
				$meta_label = wc_attribute_label( $meta_key, $product );
				if ( $meta_label ) {
					$processed_meta[] = $meta_label . ': ' . $meta_value;
				} else {
					$processed_meta[] = $meta_key . ': ' . $meta_value;
				}
			}
		}
		// Combine the processed meta fields into a single string.
		$item_meta = implode( '<br>', $processed_meta );
		return $item_meta;
	}

	/**
	 * Get image url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param string $file_type Type.
	 * @param string $file_name Name with extension.
	 */
	public static function pif_get_image_field_url( $file_type, $file_name ) {
		$img_url = '';
		if ( ! empty( $file_type ) && ! empty( $file_name ) ) {
			$plugin_url  = plugins_url() . '/product-input-fields-for-woocommerce-pro/vendor/algoritmika/product-input-fields-for-woocommerce/assets/images';
			$f_name      = explode( '.', trim( $file_name, '.' ) );
			$f_extension = end( $f_name );
			if ( ! empty( $f_extension ) && ! in_array( $file_type, array( 'image/jpeg', 'image/jpg', 'image/png' ), true ) ) {
				switch ( $f_extension ) {
					case 'pdf':
						$img_url = $plugin_url . '/pdf.png';
						break;
					case 'png':
					case 'jpeg':
					case 'jpg':
						$img_url = $plugin_url . '/image.png';
						break;
					case 'csv':
						$img_url = $plugin_url . '/csv.png';
						break;
					case 'json':
						$img_url = $plugin_url . '/json.png';
						break;
					case 'xml':
						$img_url = $plugin_url . '/xml.png';
						break;
					case 'doc':
					case 'docx':
						$img_url = $plugin_url . '/doc.png';
						break;
					case 'xls':
					case 'xlsx':
						$img_url = $plugin_url . '/excel.png';
						break;
					default:
						$img_url = $plugin_url . '/file.png';
						break;
				}
			}
		}
		return $img_url;
	}

	/**
	 * Function delete_order_file_uploads.
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 * @param number $postid POST ID.
	 */
	public static function delete_order_file_uploads( $postid ) {
		$_order = wc_get_order( $postid );
		if ( ! $_order ) {
			return;
		}
		$_items = $_order->get_items();
		$scopes = array( 'global', 'local' );
		foreach ( $scopes as $scope ) {
			foreach ( $_items as $item ) {
				$product_input_fields = maybe_unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] );
				if ( ! $product_input_fields || empty( $product_input_fields ) ) {
					return;
				}
				foreach ( $product_input_fields as $product_input_field ) {
					if ( 'file' === $product_input_field['type'] ) {
						$_value = maybe_unserialize( $product_input_field['_value'] );
						if ( isset( $_value['_tmp_name'] ) ) {
							unlink( $_value['_tmp_name'] ); //phpcs:ignore
						}
					}
				}
			}
		}
	}

	/**
	 * Function delete_file_uploads.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   int $item_id The ID of the order item.
	 */
	public static function delete_item_file_uploads( $item_id ) {
		$scopes = array( 'global', 'local' );
		foreach ( $scopes as $scope ) {
			if ( $product_input_fields = wc_get_order_item_meta( $item_id, '_' . ALG_WC_PIF_ID . '_' . $scope ) ) { //phpcs:ignore
				$product_input_fields = maybe_unserialize( $product_input_fields );
				foreach ( $product_input_fields as $product_input_field ) {
					if ( 'file' === $product_input_field['type'] ) {
						$_value = maybe_unserialize( $product_input_field['_value'] );
						if ( isset( $_value['_tmp_name'] ) ) {
							unlink( $_value['_tmp_name'] ); //phpcs:ignore
						}
					}
				}
			}
		}
	}

	public static function handle_downloads() {
		if ( current_user_can( 'edit_posts' ) && isset( $_GET['alg_wc_pif_download_file'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$file_name  = sanitize_text_field( wp_unslash( $_GET['alg_wc_pif_download_file'] ) ); // phpcs:ignore
			$file_name  = preg_replace( '/..\//', '', $file_name );
			$file_name  = preg_replace( '/.\//', '', $file_name );
			$file_array = explode( '/', $file_name );
			$file_type  = wp_check_filetype( $file_array[ count( $file_array ) - 1 ] );
			$upload_dir = alg_get_uploads_dir( 'product_input_fields' );

			if ( '' !== $file_type['ext'] && file_exists( $upload_dir . '/' . $file_name ) ) {
				$file_path = $upload_dir . '/' . $file_name;
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Cache-Control: private', false );
				header( 'Content-disposition: attachment; filename=' . $file_name );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Length: ' . filesize( $file_path ) );
				readfile( $file_path ); //phpcs:ignore
				exit();
			}
		}
	}
}
