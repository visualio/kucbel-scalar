<?php

namespace Kucbel\Scalar\Filter;

class VoidFilter extends Filter
{
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( $value )
	{
		return $value;
	}
}