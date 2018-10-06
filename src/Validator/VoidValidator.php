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
	 * @inheritdoc
	 */
	function __call( $method, $arguments )
	{
		return $this;
	}
}