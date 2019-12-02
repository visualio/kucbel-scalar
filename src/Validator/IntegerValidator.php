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
			throw new ValidatorException( $this->name, Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @param int $opt
	 * @return $this
	 */
	function value( ?int $min, ?int $max, int $opt = 0 )
	{
		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$val = $this->value;
		$nin = $opt & self::EXCL_MIN;
		$nax = $opt & self::EXCL_MAX;

		if(( $min !== null and ( $val <=> $min ) <= ( $nin ? 0 : -1 )) or ( $max !== null and ( $val <=> $max ) >= ( $nax ? 0 : 1 ))) {
			throw new ValidatorException( $this->name, Error::MIX_VALUE, ['min' => $min, 'max' => $max, 'opt' => $opt ]);
		}

		return $this;
	}

	/**
	 * @param int $div
	 * @return $this
	 */
	function modulo( int $div )
	{
		if( $div <= 0 ) {
			throw new InvalidArgumentException("Enter a positive non-zero divisor.");
		}

		if( abs( $this->value ) % $div ) {
			throw new ValidatorException( $this->name, Error::NUM_MODULO, ['div' => $div ]);
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
