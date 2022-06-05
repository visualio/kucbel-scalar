<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\FloatValidator;
use Nette\InvalidArgumentException;

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
	 * @param FloatValidator ...$items
	 */
	function __construct( string $name, FloatValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @param float ...$values
	 * @return $this
	 */
	function exist( float ...$values ) : static
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( array_diff( $values, $this->fetch() )) {
			$text = isset( $values[1] ) ? "all of the following values" : "the value";

			$this->error("Parameter \$name must contain {$text} \$list.", Error::ARR_EXIST, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param float ...$values
	 * @return $this
	 */
	function equal( float ...$values ) : static
	{
		foreach( $this->items as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param float|null $lower limit
	 * @param float|null $upper limit
	 * @param int $flag
	 * @return $this
	 */
	function value( ?float $lower, ?float $upper, int $flag = 0 ) : static
	{
		foreach( $this->items as $item ) {
			$item->value( $lower, $upper, $flag );
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function point( ?int $lower, ?int $upper ) : static
	{
		foreach( $this->items as $item ) {
			$item->point( $lower, $upper );
		}

		return $this;
	}

	/**
	 * @param float $value
	 * @return $this
	 */
	function modulo( float $value ) : static
	{
		foreach( $this->items as $item ) {
			$item->modulo( $value );
		}

		return $this;
	}
}
