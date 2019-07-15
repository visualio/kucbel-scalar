<?php

namespace Kucbel\Scalar\Input;

class VoidInput extends Input implements DetectInterface
{
	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return false;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return null;
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool
	{
		return $source ? false : true;
	}
}
