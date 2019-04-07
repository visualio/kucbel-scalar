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
		foreach( $this->list as $item ) {
			$item->equal( ...$options );
		}

		return $this;
	}

	/**
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( float $limit, bool $equal = true )
	{
		foreach( $this->list as $item ) {
			$item->max( $limit, $equal );
		}

		return $this;
	}

	/**
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( float $limit, bool $equal = true )
	{
		foreach( $this->list as $item ) {
			$item->min( $limit, $equal );
		}

		return $this;
	}

	/**
	 * @param float $min
	 * @param float $max
	 * @return $this
	 */
	function range( float $min, float $max )
	{
		foreach( $this->list as $item ) {
			$item->range( $min, $max );
		}

		return $this;
	}

	/**
	 * @param int $digit
	 * @param int $point
	 * @return $this
	 */
	function length( int $digit, int $point )
	{
		foreach( $this->list as $item ) {
			$item->length( $digit, $point );
		}

		return $this;
	}
}