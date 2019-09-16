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
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( !in_array( $this->value, $values, true )) {
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['list' => $values ]);
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
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$val = $this->value;

		if(( $min !== null and $val < $min ) or ( $max !== null and $val > $max )) {
			throw new ValidatorException( $this->name, Error::NUM_VALUE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param int|int[] $digit
	 * @return $this
	 */
	function length( $digit )
	{
		$digit = self::range( $digit );

		return $this->digit( ...$digit );
	}
}
