<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\DateValidator;
use Nette\Utils\DateTime;

/**
 * Class DateIterator
 *
 * @method DateValidator item( int $index )
 * @method DateValidator first()
 * @method DateValidator last()
 * @method DateValidator current()
 *
 * @method DateTime[] fetch()
 */
class DateIterator extends Iterator
{
	/**
	 * DateIterator constructor.
	 *
	 * @param string $name
	 * @param DateValidator ...$items
	 */
	function __construct( string $name, DateValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @param mixed ...$values
	 * @return $this
	 */
	function equal( mixed ...$values ) : static
	{
		foreach( $this->items as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param mixed | null $lower
	 * @param mixed | null $upper
	 * @param int $flag
	 * @return $this
	 */
	function value( mixed $lower, mixed $upper, int $flag = 0 ) : static
	{
		foreach( $this->items as $item ) {
			$item->value( $lower, $upper, $flag );
		}

		return $this;
	}
}
