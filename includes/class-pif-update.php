<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class PIF_Update {

	const VERSION_OPTION_KEY  = 'alg_wc_pif_version';

	/**
	 * Init the update process.
	 */
	public static function maybe_update_settings() {
		$saved_version   = get_option( self::VERSION_OPTION_KEY, '1.0.0' );

		if ( $saved_version !== PIF_VERSION ) {
			self::update_database();

			// Prevent duplicate scheduling.
			if ( ! get_option( 'pif_lite_migration_scheduled' ) ) {
				self::pif_schedule_migration_batch( 1 );
				update_option( 'pif_lite_migration_scheduled', 1 );
			}

			update_option( self::VERSION_OPTION_KEY, PIF_VERSION );
		}
	}

	/**
	 * Update database structure.
	 *
	 */
	public static function update_database() {
		$total_global_fields = 1;
		$general_settings    = array();
		$field_settings      = array();
		$license             = get_option( 'alg_wc_pif_edd_license_key_pif', '' );
		$license_status      = get_option( 'edd_license_key_pif_status', '' );

		// Future database updates can be handled here.
		$general = array(
			'enabled',
			'local_enabled',
			'frontend_position',
			'frontend_position_priority',
			'frontend_before',
			'frontend_template',
			'frontend_after',
			'frontend_required_html',
			'frontend_required_js',
			'frontend_order_table_format',
			'frontend_refill',
			'frontend_smart_textarea',
			'frontend_textarea_auto_height',
			'fill_frontend_url_parameter',
			'attach_to_admin_new_order',
			'attach_to_customer_processing_order',
			'type_checkbox_yes',
			'type_checkbox_no',
		);

		foreach ( $general as $setting ) {
			$old_option_key = "alg_wc_pif_{$setting}";
			$new_option_key = 'pif_general_settings';

			$value = get_option( $old_option_key, false );
			if ( false !== $value ) {
				$general_settings[ $setting ] = $value;
			}
		}

		update_option( 'pif_general_settings', $general_settings );

		$options = pif_get_old_options();
		foreach ( $options as $setting ) {

			if ( 'title' === $setting['type'] ) {
				continue;
			}

			$setting_id = $setting['id'];
			$index = 0;
			for ( $i = 1; $i <= $total_global_fields; $i++ ) {
				$old_option_key = "alg_wc_pif_{$setting_id}_global_{$i}";

				$value = get_option( $old_option_key, false );

				if ( false !== $value ) {
					if ( ! isset( $field_settings[ $index ] ) ) {
						$field_settings[ $index ] = array();
					}
					$field_settings[ $index ]['id'] = $i; // Store the field index for reference.
					$field_settings[ $index ]['order'] = $index + 1; // Store the field order for reference.
					if ( 'select_radio_option_type' === $setting_id ) {
						$old_array = preg_split( '/\r\n|\r|\n/', $value );
						$result    = array();
						foreach ( $old_array as $option ) {
							$result[] = array(
								'type_select_options_option'    => $option,
								'type_select_options_condition' => '',
								'type_select_options_price'     => '',
								'type_select_options_image'     => '',
							);
						}
						$field_settings[ $index ]['type_select_options'] = $result;
						$index++;
						continue;
					}

					$field_settings[ $index ][ $setting_id ] = $value;
					$index++;
				}
			}
		}

		update_option( 'pif_field_settings', $field_settings );
	}

	/**
	 * Schedule migration batch.
	 */
	public static function pif_schedule_migration_batch( $paged ) {

		if ( function_exists( 'as_schedule_single_action' ) ) {

			if ( ! as_next_scheduled_action( 'pif_run_migration_batch' ) ) {

				as_schedule_single_action(
					time() + 1,
					'pif_run_migration_batch',
					array( 'paged' => $paged ),
					'product-input-fields-for-woocommerce'
				);
			}
		} else {
			if ( ! wp_next_scheduled( 'pif_run_migration_batch' ) ) {
				wp_schedule_single_event(
					time() + 1,
					'pif_run_migration_batch',
					array( $paged )
				);
			}
		}
	}

	/**
	 * Run migration batch callback.
	 */
	public static function pif_run_migration_batch_callback( $args ) {
		$paged      = is_array( $args ) ? $args['paged'] : $args;
		$batch_size = 100;

		update_option( 'pif_migration_running', 1 );

		$products = get_posts(
			array(
				'post_type'      => 'product',
				'posts_per_page' => $batch_size,
				'paged'          => 1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'     => '_alg_wc_pif_enabled_local_1',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => '_pif_migrated_2_0',
						'compare' => 'NOT EXISTS',
					),
				),
				'fields'         => 'ids',
			)
		);

		if ( empty( $products ) ) {
			update_option( 'pif_migration_complete', 1 );
			delete_option( 'pif_migration_running' );
			return;
		}

		foreach ( $products as $product_id ) {
			self::pif_migrate_single_product( $product_id );
		}

		// If fewer products than batch size, this was the last batch.
		if ( count( $products ) < $batch_size ) {
			update_option( 'pif_migration_complete', 1 );
			delete_option( 'pif_migration_running' );
		} else {
			// Schedule next batch.
			self::pif_schedule_migration_batch( 1 );
		}
	}

	/**
	 * Migrate single product's settings.
	 */
	public static function pif_migrate_single_product( $product_id ) {
		// Skip if already migrated.
		if ( get_post_meta( $product_id, '_pif_migrated_2_0', true ) ) {
			return;
		}

		// Double safety: ensure product really uses plugin.
		if ( ! metadata_exists( 'post', $product_id, '_alg_wc_pif_enabled_local_1' ) ) {
			return;
		}

		$options             = pif_get_old_options();
		$total_local_fields  = 1;
		$field_settings      = array();

		foreach ( $options as $setting ) {

			if ( 'title' === $setting['type'] ) {
				continue;
			}
			$setting_id = $setting['id'];
			$index = 0;
			for ( $i = 1; $i <= $total_local_fields; $i++ ) {
				$old_option_key = "_alg_wc_pif_{$setting_id}_local_{$i}";
				if ( ! metadata_exists( 'post', $product_id, "_alg_wc_pif_enabled_local_{$i}" ) ) {
					continue;
				}
				$value = get_post_meta( $product_id, $old_option_key, true );

				if ( false !== $value ) {
					if ( ! isset( $field_settings[ $index ] ) ) {
						$field_settings[ $index ] = array();
					}
					$field_settings[ $index ]['id'] = $i; // Store the field index for reference.
					$field_settings[ $index ]['order'] = $index + 1; // Store the field order for reference.

					if ( 'select_radio_option_type' === $setting_id ) {
						$old_array = preg_split( '/\r\n|\r|\n/', $value );
						$result    = array();
						foreach ( $old_array as $option ) {
							$result[] = array(
								'type_select_options_option'    => $option,
								'type_select_options_condition' => '',
								'type_select_options_price'     => '',
								'type_select_options_image'     => '',
							);
						}
						$field_settings[ $index ]['type_select_options'] = $result;
						$index++;
						continue;
					}

					$field_settings[ $index ][ $setting_id ] = $value;
					$index++;
				}
			}
		}

		update_post_meta(
			$product_id,
			'pif_field_settings',
			$field_settings
		);

		update_post_meta( $product_id, '_pif_migrated_2_0', 1 );
	}

}

return new PIF_Update();