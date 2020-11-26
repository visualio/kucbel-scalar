<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\NumericValidator;

abstract class NumericIterator extends ScalarIterator
{
	/**
	 * @param int|null $lower
	 * @param int|null $upper
	 * @return $this
	 */
	function digit( ?int $lower, ?int $upper )
	{
		/** @var NumericValidator $item */
		foreach( $this->items as $item ) {
			$item->digit( $lower, $upper );
		}

		return $this;
	}
}
