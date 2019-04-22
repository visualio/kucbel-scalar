<?php

namespace Kucbel\Scalar\Validator;

class VoidValidator extends Validator
{
	/**
	 * VoidValidator constructor.
	 *
	 * @param string $name
	 */
	function __construct( string $name )
	{
		$this->name = $name;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return $this
	 */
	function __call( $name, $arguments )
	{
		return $this;
	}
}