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
	 * @param int $opt
	 * @return $this
	 */
	function value( $min, $max, int $opt = 0 )
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
		$nin = $opt & self::EXCL_MIN;
		$nax = $opt & self::EXCL_MAX;

		if(( $min !== null and ( $val <=> $min ) <= ( $nin ? 0 : -1 )) or ( $max !== null and ( $val <=> $max ) >= ( $nax ? 0 : 1 ))) {
			throw new ValidatorException( $this->name, Error::MIX_VALUE, ['min' => $min, 'max' => $max, 'opt' => $opt ]);
		}

		return $this;
	}
}
