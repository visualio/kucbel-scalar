<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\NumericValidator;

abstract class NumericIterator extends ScalarIterator
{
	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function digit( ?int $min, ?int $max = 0 )
	{
		/** @var NumericValidator $item */
		foreach( $this->list as $item ) {
			$item->digit( $min, $max );
		}

		return $this;
	}
}
