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
	 * @param DateValidator ...$list
	 */
	function __construct( string $name, DateValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param mixed|null $min
	 * @param mixed|null $max
	 * @return $this
	 */
	function value( $min, $max )
	{
		foreach( $this->list as $item ) {
			$item->value( $min, $max );
		}

		return $this;
	}
}
