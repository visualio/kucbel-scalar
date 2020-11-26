<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;

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

	/**
	 * @param bool $value
	 * @return $this
	 */
	function equal( bool $value )
	{
		if( $this->value !== $value ) {
			$this->error("Parameter \$name must be equal to \$value.", Error::MIX_EQUAL, ['value' => $value ]);
		}

		return $this;
	}
}
