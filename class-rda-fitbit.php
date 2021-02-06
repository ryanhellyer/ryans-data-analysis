<?php

class RDA_Fitbit {

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
		return;

		$lines = explode( "\n", $contents );
		foreach ( $lines as $line ) {
			$row = explode( ',', $line );

			// If first column is a time, then we want to process this row ...
			$time = strtotime( trim( str_replace( '"', '', $row[0] ) ) );

			$time = strtotime( trim( str_replace( '"', '', $row[0] . ' ' . $row[3] ) ) );
			if ( '' != $time ) {
				$data = array();

				// Add data types.
				$data = $this->add_mood( $data, $row );
				$data = $this->add_activities( $data, $row );
				$data = $this->add_note( $data, $row );

				$array[ $time ] = $data;
			}
		}

		return $array;
	}

	/**
	 *
	 * @param array
	 * @param string
	 * @return array
	 */
	private function add_mood( $data, $row ) {
		$mood = 'error';

		switch ( $row[4] ) {
			case 'rad':
				$mood = 4;
				break;
			case 'good':
				$mood = 3;
				break;
			case 'meh':
				$mood = 2;
				break;
			case 'bad':
				$mood = 1;
				break;
			case 'awful':
				$mood = 0;
				break;
		}

		$data['mood'] = $mood;

		return $data;
	}

	/**
	 *
	 * @param array
	 * @param array
	 * @return array
	 */
	private function add_activities( $data, $row ) {
		$activities = $row[5];

		$activities = explode( '|', $row[5] );
		foreach ( $activities as $key => $activity ) {

			$activity = trim( $activity );
			$activity = str_replace( '"', '', $activity );
			if ( '' !== $activity ) {
				$data['activities'] = $activity;
			}

		}

		return $data;
	}

	/**
	 *
	 * @param array
	 * @param array
	 * @return array
	 */
	private function add_note( $data, $row ) {
		$note_title = trim( str_replace( '"', '', $row[6] ) );
		$note_text  = trim( str_replace( '"', '', $row[7] ) );

		$note = '';

		if ( '' !== $note_title  ) {
			$note .= $note_title;
		}

		if ( '' !== $note_title && '' !== $note_text ) {
			$note .= ' - ';
		}

		if ( '' !== $note_text ) {
			$note .= $note_text;
		}

		if ( '' !== $note ) {
			$data['note'] = $note;
		}

		return $data;
	}

}
