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
		$this->value = $value ?: 0.;
	}

	/**
	 * @param float ...$values
	 * @return $this
	 */
	function equal( float ...$values ) : static
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( !in_array( $this->value, $values, true )) {
			$text = isset( $values[1] ) ? 'one of the following' : 'equal to';

			$this->error("Parameter \$name must be {$text} \$list.", Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param float|null $lower limit
	 * @param float|null $upper limit
	 * @param int $flag
	 * @return $this
	 */
	function value( ?float $lower, ?float $upper, int $flag = 0 ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$match = true;

		if( $lower !== null ) {
			if(( $this->value <=> $lower ) <= ( $flag & self::EXCL_LOWER ? 0 : -1 )) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if(( $this->value <=> $upper ) >= ( $flag & self::EXCL_UPPER ? 0 : 1 )) {
				$match = false;
			}
		}

		if( !$match ) {
			$text = '';

			if( $lower !== null ) {
				$text .= $flag & self::EXCL_LOWER ? " greater than \$lower" : " equal or greater than \$lower";
			}

			if( $lower !== null and $upper !== null ) {
				$text .= " and";
			}

			if( $upper !== null ) {
				$text .= $flag & self::EXCL_UPPER ? " less than \$upper" : " equal or less than \$upper";
			}

			$this->error("Parameter \$name must be{$text}.", Error::MIX_VALUE, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function point( ?int $lower, ?int $upper ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$match = true;

		if( $lower !== null and $lower !== 0 ) {
			if( $lower < 0 ) {
				throw new InvalidArgumentException("Enter a positive lower limit.");
			}

			if( $this->value === round( $this->value, $lower - 1 )) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 0 ) {
				throw new InvalidArgumentException("Enter a positive upper limit.");
			}

			if( $this->value !== round( $this->value, $upper )) {
				$match = false;
			}
		}

		if( !$match ) {
			if( $lower === $upper ) {
				$text = "exactly \$lower";
			} elseif( $upper === null ) {
				$text = "at least \$lower";
			} elseif( $lower === null ) {
				$text = "at most \$upper";
			} else {
				$text = "between \$lower and \$upper";
			}

			$this->error("Parameter \$name must have {$text} decimal digits.", Error::NUM_POINT, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @param float $value
	 * @return $this
	 */
	function modulo( float $value ) : static
	{
		if( $value <= 0 ) {
			throw new InvalidArgumentException("Enter a positive non-zero value.");
		}

		if( fmod( abs( $this->value ), $value )) {
			$this->error("Parameter \$name must be divisible by \$value.", Error::NUM_MODULO, ['value' => $value ]);
		}

		return $this;
	}
}
