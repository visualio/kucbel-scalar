<?php

namespace Kucbel\Scalar\Filter;

interface FilterInterface
{
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( string $name, $value );
}
