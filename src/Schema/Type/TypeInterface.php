<?php

namespace Kucbel\Scalar\Schema\Type;

use Kucbel\Scalar\Validator\MixedValidator;

interface TypeInterface
{
	/**
	 * @param MixedValidator $value
	 * @return mixed
	 */
	function fetch( MixedValidator $value ) : mixed;
}
