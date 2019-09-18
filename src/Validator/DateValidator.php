<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime;

/**
 * Class DateValidator
 *
 * @method DateTime fetch()
 */
class DateValidator extends Validator
{
	/**
	 * DateValidator constructor.
	 *
	 * @param string $name
	 * @param DateTime $value
	 */
	function __construct( string $name, DateTime $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param mixed ...$values
	 * @return $this
	 */
	function equal( ...$values )
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		foreach( $values as $i => $value ) {
			$values[ $i ] = DateTime::from( $value );
		}

		if( !in_array( $this->value, $values )) {
			throw new ValidatorException( $this->name, Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param mixed|null $min
	 * @param mixed|null $max
	 * @return $this
	 */
	function value( $min, $max )
	{
		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( $min !== null ) {
			$min = DateTime::from( $min );
		}

		if( $max !== null ) {
			$max = DateTime::from( $max );
		}

		$val = $this->value;

		if(( $min !== null and $val < $min ) or ( $max !== null and $val > $max )) {
			throw new ValidatorException( $this->name, Error::MIX_VALUE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}
