<?php

namespace Kucbel\Scalar\Output;

use Kucbel\Scalar\Input\ConfigInput;

class ConfigOutput extends ConfigInput implements OutputInterface
{
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$name = $this->alias( $name );

		if( $value === null ) {
			$done = ini_set( $name, '');
		} elseif( is_bool( $value )) {
			$done = ini_set( $name, $value ? 1 : 0 );
		} elseif( is_scalar( $value )) {
			$done = ini_set( $name, $value );
		} else {
			$done = false;
		}

		if( $done === false ) {
			throw new OutputException("Unable to modify configuration value.");
		}
	}
}