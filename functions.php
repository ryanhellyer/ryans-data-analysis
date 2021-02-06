<?php

// Shim for PHP 8 functionality.
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( string $haystack, string $needle ) {

		if ( strpos( $haystack, $needle ) !== false) {
			return true;
		}

		return false;
	}
}

// Shim for WordPress's absint.
if ( ! function_exists( 'absint' ) ) {
	function absint( $int ) {
		return intval( $int );
	}
}
