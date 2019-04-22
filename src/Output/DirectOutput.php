<?php

namespace Kucbel\Scalar\Output;

use ArrayAccess;

class DirectOutput extends Output
{
	/**
	 * @var iterable | array | null
	 */
	private $values;

	/**
	 * DirectOutput constructor.
	 *
	 * @param ArrayAccess $values
	 */
	function __construct( ArrayAccess $values = null )
	{
		$this->values = $values;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$this->values[ $name ] = $value;
	}

	/**
	 * @return iterable | array | null
	 */
	function fetch() : ?iterable
	{
		return $this->values;
	}
}