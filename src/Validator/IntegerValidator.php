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
			$text = isset( $values[1] ) ? 'one of the following' : 'equal to';

			$this->error("Parameter \$name must be {$text} \$list.", Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $lower
	 * @param int|null $upper
	 * @param int $flag
	 * @return $this
	 */
	function value( ?int $lower, ?int $upper, int $flag = 0 )
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
	 * @param int $value
	 * @return $this
	 */
	function modulo( int $value )
	{
		if( $value <= 0 ) {
			throw new InvalidArgumentException("Enter a positive non-zero value.");
		}

		if( abs( $this->value ) % $value ) {
			$this->error("Parameter \$name must be divisible by \$value.", Error::NUM_MODULO, ['value' => $value ]);
		}

		return $this;
	}
}
