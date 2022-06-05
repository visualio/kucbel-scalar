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
	 * @param BoolValidator ...$items
	 */
	function __construct( string $name, BoolValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @param bool $value
	 * @return $this
	 */
	function equal( bool $value ) : static
	{
		foreach( $this->items as $item ) {
			$item->equal( $value );
		}

		return $this;
	}
}
