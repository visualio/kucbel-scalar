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
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param mixed|null $min
	 * @param mixed|null $max
	 * @param bool $exc
	 * @return $this
	 */
	function value( $min, $max, bool $exc = false )
	{
		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		if( $min !== null ) {
			$min = DateTime::from( $min );
		}

		if( $max !== null ) {
			$max = DateTime::from( $max );
		}

		if( $min !== null and ( $this->value <=> $min ) <= ( $exc ? 0 : -1 )) {
			throw new ValidatorException( $this->name, Error::DATE_VALUE, ['min' => $min, 'max' => $max ]);
		}

		if( $max !== null and ( $this->value <=> $max ) >= ( $exc ? 0 : 1 )) {
			throw new ValidatorException( $this->name, Error::DATE_VALUE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}
