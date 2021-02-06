<?php

class RDA_Fitbit extends RDA_Core {


	private $kept_types = array(
		'Body',
		'Activities',
		'Sleep',
	);

	/**
	 * Get data.
	 */
	public function get_data( $files ) {
		$data = array();

		foreach ( $files as $file ) {
			$contents = file_get_contents( RDA_DATA_LOCATION . $file );
			$chunks     = $this->convert_to_chunks( $contents );
		}

		$data = $this->process_body_data( $chunks['Body'] );
//		$data = $this->process_activities_data( $chunks['Activities'] );
//		$data = $this->process_sleep_data( $chunks['Sleep'] );

		return $data;
	}

	/**
	 * Convert CSV to array.
	 *
	 * @param string $contents The raw CSV file contents.
	 * @return array The data.
	 */
	private function convert_to_chunks( $contents ) {
		$chunks = array();

		$lines = explode( "\n", $contents );

		$rejected_types = array(
			'Foods',
			'Food Log',
		);

		$types = array_merge( $this->kept_types, $rejected_types );

		foreach ( $lines as $line ) {

			if ( in_array( $line, $types ) ) {
				$type = $line;
			} else if ( str_contains( $line, 'Food Log' ) ) {
				$type = 'Food Log';
			}

			if ( in_array( $type, $rejected_types ) ) {
				continue;
			}

			if ( isset( $type ) ) {
				$chunks[ $type ][] = $line;
			}

		}

		return $chunks;
	}

	/**
	 */
	private function process_body_data( $chunk ) {
		$data = array();

		unset( $chunk[0] );
		unset( $chunk[1] );

		foreach ( $chunk as $line ) {
			$columns = explode( ',', $line );

			$date = $this->cleanup( $columns[0] );
			if ( '' == $date ) {
				continue;
			}
			$start_time = strtotime( $date );
			$middle_time = $start_time + ( 24 * 60 * 60 ); // add 12 hours to make sure it's in the middle of the day.

			$weight = $this->cleanup( $columns[1] );
			$bmi    = $this->cleanup( $columns[2] );
			$fat    = $this->cleanup( $columns[3] );

			$data[ $middle_time ] = array(
				'weight'     => $weight,
				'bmi'        => $bmi,
				'fat'        => $fat,
			);
		}

		return $data;
	}

}
