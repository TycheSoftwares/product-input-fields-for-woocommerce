var $pif_lite_tyche_plugin_deactivation_modal = {},
	$tyche_plugin_name = 'pif_lite';

( function() {

	if ( 'undefined' === typeof tyche.plugin_deactivation || 'undefined' === typeof window[ `tyche_plugin_deactivation_${$tyche_plugin_name}_js` ] ) {
		return;
	}

	$pif_lite_tyche_plugin_deactivation_modal = tyche.plugin_deactivation.modal( $tyche_plugin_name, window[ `tyche_plugin_deactivation_${$tyche_plugin_name}_js` ] );

	if ( '' !== $pif_lite_tyche_plugin_deactivation_modal ) {
		tyche.plugin_deactivation.events.listeners( window[ `tyche_plugin_deactivation_${$tyche_plugin_name}_js` ], $pif_lite_tyche_plugin_deactivation_modal, $tyche_plugin_name );
	}
} )();
