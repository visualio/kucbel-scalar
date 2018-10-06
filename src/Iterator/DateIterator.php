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
	 * @param mixed $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( $limit, bool $equal = true )
	{
		return $this->each('max', $limit, $equal );
	}

	/**
	 * @param mixed $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( $limit, bool $equal = true )
	{
		return $this->each('min', $limit, $equal );
	}

	/**
	 * @param mixed $min
	 * @param mixed $max
	 * @return $this
	 */
	function range( $min, $max )
	{
		return $this->each('range', $min, $max );
	}
}