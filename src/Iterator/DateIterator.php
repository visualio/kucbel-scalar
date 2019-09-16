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
	 * @param mixed ...$values
	 * @return $this
	 */
	function equal( ...$values )
	{
		foreach( $this->list as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param mixed|null $min
	 * @param mixed|null $max
	 * @param bool $exc
	 * @return $this
	 */
	function value( $min, $max, bool $exc = false )
	{
		foreach( $this->list as $item ) {
			$item->value( $min, $max, $exc );
		}

		return $this;
	}
}
