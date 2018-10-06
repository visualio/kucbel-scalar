<?php

namespace Kucbel\Scalar\Input;

use ArrayAccess;

class ArrayInput extends Input
{
	/**
	 * @var ArrayAccess
	 */
	private $array;

	/**
	 * ArrayInput constructor.
	 *
	 * @param ArrayAccess $array
	 */
	function __construct( ArrayAccess $array )
	{
		$this->array = $array;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->array[ $name ] ?? null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return isset( $this->array[ $name ] );
	}
}