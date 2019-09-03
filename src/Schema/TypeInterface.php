<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Validator\MixedValidator;

interface TypeInterface
{
	/**
	 * @param MixedValidator $validator
	 * @return mixed
	 */
	function fetch( MixedValidator $validator );
}
