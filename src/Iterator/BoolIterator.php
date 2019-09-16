<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\BoolValidator;

/**
 * Class BoolIterator
 *
 * @method BoolValidator item( int $index )
 * @method BoolValidator first()
 * @method BoolValidator last()
 * @method BoolValidator current()
 *
 * @method bool[] fetch()
 */
class BoolIterator extends Iterator
{
	/**
	 * BoolIterator constructor.
	 *
	 * @param string $name
	 * @param BoolValidator ...$list
	 */
	function __construct( string $name, BoolValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param bool $value
	 * @return $this
	 */
	function equal( bool $value )
	{
		foreach( $this->list as $item ) {
			$item->equal( $value );
		}

		return $this;
	}
}
