<?php

class RDA_Core {

	/**
	 * Cleanup strings.
	 * 
	 * @param string $content
	 * @return string
	 */
	protected function cleanup( $content ) {
		return trim( str_replace( '"', '', $content ) );
	}

}
