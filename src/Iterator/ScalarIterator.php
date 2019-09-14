<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ScalarValidator;
use Kucbel\Scalar\Validator\ValidatorException;

abstract class ScalarIterator extends Iterator
{
	/**
	 * @return $this
	 */
	function unique()
	{
		$values = $this->fetch();

		if( array_diff_key( $values, array_unique( $values, SORT_REGULAR ))) {
			throw new ValidatorException( $this->name, Error::ARR_UNIQUE );
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
		foreach( $this->list as $item ) {
			$item->match( $regex );
		}

		return $this;
	}
}
