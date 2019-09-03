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
class FloatIterator extends NumericIterator
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
	 * @param float ...$values
	 * @return $this
	 */
	function equal( float ...$values )
	{
		foreach( $this->list as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param float|null $min
	 * @param float|null $max
	 * @return $this
	 */
	function value( ?float $min, ?float $max )
	{
		foreach( $this->list as $item ) {
			$item->value( $min, $max );
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function point( ?int $min, ?int $max = 0 )
	{
		foreach( $this->list as $item ) {
			$item->point( $min, $max );
		}

		return $this;
	}
}
