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
	function digit( ?int $min, ?int $max )
	{
		if(( $min !== null and $min < 0 ) or ( $max !== null and $max < 0 )) {
			throw new InvalidArgumentException("Enter a positive length limit.");
		} elseif( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$val = abs( $this->value );

		if(( $min !== null and $min !== 0 and $val < 10 ** ( $min - 1 )) or ( $max !== null and $val >= 10 ** $max )) {
			throw new ValidatorException( $this->name, Error::NUM_DIGIT, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}
