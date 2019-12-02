<?php

namespace Kucbel\Scalar\Validator;
use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;

/**
 * Class FloatValidator
 *
 * @method float fetch()
 */
class FloatValidator extends NumericValidator
{
	/**
	 * FloatValidator constructor.
	 *
	 * @param string $name
	 * @param float $value
	 */
	function __construct( string $name, float $value )
	{
		$this->name = $name;
		$this->value = $value ? $value : 0.;
	}

	/**
	 * @param float ...$values
	 * @return $this
	 */
	function equal( float ...$values )
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
	 * @param float|null $min
	 * @param float|null $max
	 * @param int $opt
	 * @return $this
	 */
	function value( ?float $min, ?float $max, int $opt = 0 )
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
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function point( ?int $min, ?int $max )
	{
		if(( $min !== null and $min < 0 ) or ( $max !== null and $max < 0 )) {
			throw new InvalidArgumentException("Enter a positive length limit.");
		} elseif( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$val = $this->value;

		if(( $min !== null and $min !== 0 and $val === round( $val, $min - 1 )) or ( $max !== null and $val !== round( $val, $max ))) {
			throw new ValidatorException( $this->name, Error::NUM_POINT, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param float $div
	 * @return $this
	 */
	function modulo( float $div )
	{
		if( $div <= 0 ) {
			throw new InvalidArgumentException("Enter a positive non-zero divisor.");
		}

		if( fmod( abs( $this->value ), $div )) {
			throw new ValidatorException( $this->name, Error::NUM_MODULO, ['div' => $div ]);
		}

		return $this;
	}

	/**
	 * @param int|int[] $digit
	 * @param int|int[] $point
	 * @return $this
	 */
	function length( $digit, $point )
	{
		$digit = self::range( $digit );
		$point = self::range( $point );

		return $this->digit( ...$digit )->point( ...$point );
	}
}
