<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ScalarValidator;

abstract class ScalarIterator extends Iterator
{
	/**
	 * @return $this
	 */
	function unique()
	{
		$values = $this->fetch();
		$checks = [];

		foreach( $values as $value ) {
			if( isset( $checks["{$value}"] )) {
				$this->error("Parameter \$name must contain unique values.", Error::ARR_UNIQUE );
			}

			$checks["{$value}"] = true;
		}

		return $this;
	}

	/**
	 * @param string $regex
	 * @return $this
	 */
	function match( string $regex )
	{
		/** @var ScalarValidator $item */
		foreach( $this->items as $item ) {
			$item->match( $regex );
		}

		return $this;
	}
}
