<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ScalarValidator;

abstract class ScalarIterator extends Iterator
{
	/**
	 * @var ScalarValidator[]
	 */
	protected $list;

	/**
	 * @param string $regex
	 * @return $this
	 */
	function match( string $regex )
	{
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