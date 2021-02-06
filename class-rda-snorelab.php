<?php

class RDA_Snorelab {

	/**
	 * Get data.
	 */
	public function get_data( $files ) {
		$data = array();

		foreach ( $files as $file ) {
			$contents = file_get_contents( RDA_DATA_LOCATION . $file );
			$data     = $this->convert_to_array( $contents );
		}

		return $data;
	}

	/**
	 * Convert CSV to array.
	 *
	 * @param string $contents The raw CSV file contents.
	 * @return array The data.
	 */
	private function convert_to_array( $contents ) {
		$data = array();

		$lines = explode( "\n", $contents );
		foreach ( $lines as $line ) {
			$row = explode( ',', $line );

			if ( ! isset( $row[1] ) ) {
				continue;
			}

			// If first column is a time, then we want to process this row ...
			$time = strtotime( $this->cleanup( $row[0] ) );

			if ( '' != $time ) {
				$data = array();

				// Add data.
				$data = $this->add_date( $data, $row );
				$data = $this->add_snoring_times( $data, $row );

				$array[ $time ] = $data;
			}
		}

		return $array;
	}

	/**
	 * Add snoring times.
	 *
	 * @param array
	 * @param string
	 * @return array
	 */
	private function add_snoring_times( $data, $row ) {
		$time_snoring = $this->convert_to_amount_of_time( $row[4] );

		$mild_snoring_percentage = $this->cleanup( $row[6] ) * 100;
		$loud_snoring_percentage = $this->cleanup( $row[7] ) * 100;
		$epic_snoring_percentage = $this->cleanup( $row[8] ) * 100;

		$total_snore_percentage = absint( $mild_snoring_percentage + $loud_snoring_percentage + $epic_snoring_percentage );

		$mild_snoring_fraction = 0;
		$loud_snoring_fraction = 0;
		$epic_snoring_fraction = 0;
		if ( 0 !== $total_snore_percentage ) {
			$mild_snoring_fraction = $mild_snoring_percentage / $total_snore_percentage;
			$loud_snoring_fraction = $loud_snoring_percentage / $total_snore_percentage;
			$epic_snoring_fraction = $epic_snoring_percentage / $total_snore_percentage;
		}

		$mild_snoring_time = absint( $mild_snoring_fraction * $time_snoring );
		$loud_snoring_time = absint( $loud_snoring_fraction * $time_snoring );
		$epic_snoring_time = absint( $epic_snoring_fraction * $time_snoring );

		$times = array(
			'mild' => $mild_snoring_time,
			'loud' => $loud_snoring_time,
			'epic' => $epic_snoring_time,
		);


		$data['times'] = $times;

		return $data;
	}

	/**
	 * Add date.
	 * 
	 * @param array
	 * @param string
	 * @return array
	 */
	private function add_date( $data, $row ) {

		$data['start_time'] = strtotime( $this->cleanup( $row[1] ) );
		$data['end_time']   = strtotime( $this->cleanup( $row[2] ) );

		return $data;
	}

	/**
	 * Cleanup strings.
	 * 
	 * @param string $content
	 * @return string
	 */
	private function cleanup( $content ) {
		return trim( str_replace( '"', '', $content ) );
	}

	/**
	 * Convert to amount of time.
	 * 
	 * @param string $content
	 * @return int
	 */
	private function convert_to_amount_of_time( $time ) {
		$time = $this->cleanup( $time );

		$array = explode( ':', $time );
		$hours = $array[0] * 60 * 60;
		$minutes = $array[1] * 60;
		$seconds = $array[2];

		$time = $hours + $minutes + $seconds;

		return $time;
	}

}
