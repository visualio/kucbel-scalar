<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Validator\MixedValidator;

interface TypeInterface
{
	/**
	 * @param MixedValidator $value
	 * @return mixed
	 */
	function fetch( MixedValidator $value );
}
