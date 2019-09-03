<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;

abstract class NumericValidator extends ScalarValidator
{
	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function digit( ?int $min, ?int $max = 0 )
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

		$val = abs( $this->value );

		if( $min !== null ) {
			if( $min < 0 ) {
				throw new InvalidArgumentException("Enter positive length limit.");
			}

			if( $val < 10 ** ( $min - 1 )) {
				throw new ValidatorException( $this->name, Error::NUM_DIGIT, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 0 ) {
				throw new InvalidArgumentException("Enter positive length limit.");
			}

			if( $val >= 10 ** $max ) {
				throw new ValidatorException( $this->name, Error::NUM_DIGIT, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
	}
}
