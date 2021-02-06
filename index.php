<?php

define( 'RDA_DATA_LOCATION', dirname( __FILE__ ) . '/data/' );

// Error display.
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

// Load required files.
require( 'functions.php' );
require( 'class-rda-core.php' );
require( 'class-rda-daylio.php' );
require( 'class-rda-snorelab.php' );
require( 'class-rda-fitbit.php' );

// Get CSV files.
$types = array( 'daylio', 'snorelab', 'fitbit' );
$files = array();
foreach ( scandir( RDA_DATA_LOCATION ) as $file ) {
	if ( '.csv' === substr( $file, -4 ) ) {

		foreach ( $types as $type ) {
			if ( str_contains( $file, $type ) ) {
				$files[ $type ][] = $file;
			}
		}
	}
}


$daylio = new RDA_Daylio();
$daylio_data = $daylio->get_data( $files['daylio'] );

$snorelab = new RDA_Snorelab();
$snorelab_data = $snorelab->get_data( $files['snorelab'] );

$fitbit = new RDA_Fitbit();
$fitbit_data = $fitbit->get_data( $files['fitbit'] );


// MAKE SURE THAT SAME KEY CAN BE MERGED IN TO EACH OTHER AT ALL POINTS ... 
$d = array_replace( $snorelab_data, $daylio_data );
$d = array_replace( $d, $fitbit_data );
ksort( $d );

print_r( $d );

die( 'DONE!' );
