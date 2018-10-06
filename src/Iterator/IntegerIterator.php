<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\IntegerValidator;

/**
 * Class IntegerIterator
 *
 * @method IntegerValidator item( int $index )
 * @method IntegerValidator first()
 * @method IntegerValidator last()
 * @method IntegerValidator current()
 *
 * @method int[] fetch()
 */
class IntegerIterator extends ScalarIterator
{
	/**
	 * IntegerIterator constructor.
	 *
	 * @param string $name
	 * @param IntegerValidator ...$list
	 */
	function __construct( string $name, IntegerValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param int ...$options
	 * @return $this
	 */
	function equal( int ...$options )
	{
		return $this->each('equal', ...$options );
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( int $limit, bool $equal = true )
	{
		return $this->each('max', $limit, $equal );
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( int $limit, bool $equal = true )
	{
		return $this->each('min', $limit, $equal );
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function range( int $min, int $max )
	{
		return $this->each('min', $min, $max );
	}

	/**
	 * @param int $digits
	 * @return $this
	 */
	function length( int $digits )
	{
		return $this->each('length', $digits );
	}
}