<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;

/**
 * Class IntegerValidator
 *
 * @method int fetch()
 */
class IntegerValidator extends NumericValidator
{
	/**
	 * IntegerValidator constructor.
	 *
	 * @param string $name
	 * @param int $value
	 */
	function __construct( string $name, int $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param int ...$values
	 * @return $this
	 */
	function equal( int ...$values )
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one parameter.");
		}

		if( !in_array( $this->value, $values, true )) {
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['val' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function value( ?int $min, ?int $max )
	{
		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		if( $min !== null and $this->value < $min ) {
			throw new ValidatorException( $this->name, Error::NUM_VALUE, ['min' => $min, 'max' => $max ]);
		}

		if( $max !== null and $this->value > $max ) {
			throw new ValidatorException( $this->name, Error::NUM_VALUE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}