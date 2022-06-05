<?php

namespace Kucbel\Scalar\Filter;

interface FilterInterface
{
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( mixed $value ) : mixed;
}
