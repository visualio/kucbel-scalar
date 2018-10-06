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
		$values = [];

		foreach( $this->list as $item ) {
			$value = (string) $item->fetch();

			if( isset( $values[ $value ] )) {
				$this->error( Error::ARR_UNIQUE );
			}

			$values[ $value ] = true;
		}

		return $this;
	}
}