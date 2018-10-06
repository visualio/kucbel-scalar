<?php

namespace Kucbel\Scalar\Validator;

/**
 * Class BoolValidator
 *
 * @method bool fetch()
 */
class BoolValidator extends Validator
{
	/**
	 * BoolValidator constructor.
	 *
	 * @param string $name
	 * @param bool $value
	 */
	function __construct( string $name, bool $value )
	{
		$this->name = $name;
		$this->value = $value;
	}
}