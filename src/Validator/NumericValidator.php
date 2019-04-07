<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;

abstract class NumericValidator extends ScalarValidator
{
	/**
	 * @param int $limit
	 * @return $this
	 */
	protected function digit( int $limit )
	{
		if( abs( $this->value ) >= pow( 10, $limit )) {
			$this->error( Error::NUM_DIGIT, ['dig' => $limit ]);
		}

		return $this;
	}
}