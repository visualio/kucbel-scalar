<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\Utils\Strings;

abstract class ScalarValidator extends Validator
{
	/**
	 * @param string $regex
	 * @return $this
	 */
	function match( string $regex )
	{
		if( !Strings::match( $this->value, $regex )) {
			throw new ValidatorException( $this->name, Error::MIX_MATCH, ['exp' => $regex ]);
		}

		return $this;
	}
}
