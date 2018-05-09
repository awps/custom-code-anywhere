<?php 

new TrackingCodeInstaller\Config;

$saved_tracking_codes = get_option( 'tc_installer_data' );

if( !empty( $saved_tracking_codes ) ){
	foreach ($saved_tracking_codes as $tcid => $track) {
		new TrackingCodeInstaller\Injector( $tcid, $track );
	}
}