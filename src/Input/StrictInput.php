<?php

namespace Kucbel\Scalar\Input;

abstract class StrictInput extends Input
{
	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->get( $name, $this ) !== $this;
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	abstract function get( string $name, $null = null );
}