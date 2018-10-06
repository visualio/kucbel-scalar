<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\FloatValidator;

/**
 * Class FloatIterator
 *
 * @method FloatValidator item( int $index )
 * @method FloatValidator first()
 * @method FloatValidator last()
 * @method FloatValidator current()
 *
 * @method float[] fetch()
 */
class FloatIterator extends ScalarIterator
{
	/**
	 * FloatIterator constructor.
	 *
	 * @param string $name
	 * @param FloatValidator ...$list
	 */
	function __construct( string $name, FloatValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param float ...$options
	 * @return $this
	 */
	function equal( float ...$options )
	{
		return $this->each('equal', ...$options );
	}

	/**
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( float $limit, bool $equal = true )
	{
		return $this->each('max', $limit, $equal );
	}

	/**
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( float $limit, bool $equal = true )
	{
		return $this->each('min', $limit, $equal );
	}

	/**
	 * @param float $min
	 * @param float $max
	 * @return $this
	 */
	function range( float $min, float $max )
	{
		return $this->each('range', $min, $max );
	}

	/**
	 * @param int $digits
	 * @param int $decimals
	 * @return $this
	 */
	function length( int $digits, int $decimals )
	{
		return $this->each('length', $digits, $decimals );
	}
}