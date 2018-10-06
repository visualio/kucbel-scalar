<?php

namespace Kucbel\Scalar\Output;

use Kucbel\Scalar\Input\EnvironmentInput;

class EnvironmentOutput extends EnvironmentInput implements OutputInterface
{
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$name = $this->alias( $name );

		if( $value === null ) {
			$done = putenv( $name );
		} elseif( is_bool( $value )) {
			$value = $value ? 1 : 0;
			$done = putenv("$name=$value");
		} elseif( is_scalar( $value )) {
			$done = putenv("$name=$value");
		} else {
			$done = false;
		}

		if( $done === false ) {
			throw new OutputException("Unable to modify environment value.");
		}
	}
}