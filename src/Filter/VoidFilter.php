<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class VoidFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( mixed $value ) : mixed
	{
		return $value;
	}
}
