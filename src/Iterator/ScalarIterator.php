<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ScalarValidator;
use Kucbel\Scalar\Validator\ValidatorException;

abstract class ScalarIterator extends Iterator
{
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

	/**
	 * @return $this
	 */
	function unique()
	{
		$format = $this instanceof FloatIterator;
		$values = null;

		/** @var ScalarValidator $item */
		foreach( $this->list as $item ) {
			if( $format ) {
				$value = (string) $item->fetch();
			} else {
				$value = $item->fetch();
			}

			if( isset( $values[ $value ] )) {
				throw new ValidatorException( $this->name, Error::ARR_UNIQUE );
			}

			$values[ $value ] = true;
		}

		return $this;
	}
}