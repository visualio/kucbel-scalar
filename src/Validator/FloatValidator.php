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
		$this->value = $value;
	}

	/**
	 * @param float ...$values
	 * @return $this
	 */
	function equal( float ...$values )
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
	 * @param float|null $min
	 * @param float|null $max
	 * @return $this
	 */
	function value( ?float $min, ?float $max )
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

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function point( ?int $min, ?int $max = 0 )
	{
		if( $min !== null and $max !== null and $min > $max ) {
			[ $min, $max ] = [ $max, $min ];
		}

		if( $min === 0 ) {
			$min = null;
		}

		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		if( $min !== null ) {
			if( $min < 0 ) {
				throw new InvalidArgumentException("Enter positive length limit.");
			}

			$val = $this->value * pow( 10, $min - 1 );

			if( ceil( $val ) === floor( $val )) {
				throw new ValidatorException( $this->name, Error::NUM_POINT, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 0 ) {
				throw new InvalidArgumentException("Enter positive length limit.");
			}

			if( $this->value !== round( $this->value, $max )) {
				throw new ValidatorException( $this->name, Error::NUM_POINT, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
	}
}