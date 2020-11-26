<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;

abstract class NumericValidator extends ScalarValidator
{
	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function digit( ?int $lower, ?int $upper )
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$check = $this->value < 0 ? - $this->value : $this->value;
		$match = true;

		if( $lower !== null and $lower !== 0 ) {
			if( $lower < 0 ) {
				throw new InvalidArgumentException("Enter a positive lower limit.");
			}

			if( $check < 10 ** ( $lower - 1 )) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 0 ) {
				throw new InvalidArgumentException("Enter a positive upper limit.");
			}

			if( $check >= 10 ** $upper ) {
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

			$this->error("Parameter \$name must have {$text} digits.", Error::NUM_DIGIT, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}
}
