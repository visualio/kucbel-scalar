<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;

abstract class ScalarIterator extends Iterator
{
	/**
	 * @param string $regex
	 * @return $this
	 */
	function match( string $regex )
	{
		return $this->each('match', $regex );
	}

	/**
	 * @return $this
	 */
	function unique()
	{
		$string = $this instanceof FloatIterator;
		$values = [];

		foreach( $this->list as $item ) {
			if( $string ) {
				$value = (string) $item->fetch();
			} else {
				$value = $item->fetch();
			}

			if( isset( $values[ $value ] )) {
				$this->error( Error::ARR_UNIQUE );
			}

			$values[ $value ] = true;
		}

		return $this;
	}
}