<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime;
use Throwable;

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
	 * @param mixed|null $min
	 * @param mixed|null $max
	 * @return $this
	 */
	function value( $min, $max )
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

		if( $min !== null and $this->value < $min ) {
			throw new ValidatorException( $this->name, Error::DATE_VALUE, ['min' => $min, 'max' => $max ]);
		}

		if( $max !== null and $this->value > $max ) {
			throw new ValidatorException( $this->name, Error::DATE_VALUE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}