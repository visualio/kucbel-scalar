<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class VoidFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( string $name, $value )
	{
		return $value;
	}
}
