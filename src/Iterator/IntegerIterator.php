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
		foreach( $this->list as $item ) {
			$item->equal( ...$options );
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( int $limit, bool $equal = true )
	{
		foreach( $this->list as $item ) {
			$item->max( $limit, $equal );
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( int $limit, bool $equal = true )
	{
		foreach( $this->list as $item ) {
			$item->min( $limit, $equal );
		}

		return $this;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function range( int $min, int $max )
	{
		foreach( $this->list as $item ) {
			$item->range( $min, $max );
		}

		return $this;
	}

	/**
	 * @param int $digit
	 * @return $this
	 */
	function length( int $digit )
	{
		foreach( $this->list as $item ) {
			$item->length( $digit );
		}

		return $this;
	}
}