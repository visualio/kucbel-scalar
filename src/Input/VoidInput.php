<?php

namespace Kucbel\Scalar\Input;

class VoidInput extends Input
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
}