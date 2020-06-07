<?php

namespace Kucbel\Scalar\Filter;

interface FilterInterface
{
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function value( $value );
}
