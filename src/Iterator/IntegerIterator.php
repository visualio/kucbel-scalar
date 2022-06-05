<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\IntegerValidator;
use Nette\InvalidArgumentException;

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
class IntegerIterator extends NumericIterator
{
	/**
	 * IntegerIterator constructor.
	 *
	 * @param string $name
	 * @param IntegerValidator ...$items
	 */
	function __construct( string $name, IntegerValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @param int ...$values
	 * @return $this
	 */
	function exist( int ...$values ) : static
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
	 * @param int ...$values
	 * @return $this
	 */
	function equal( int ...$values ) : static
	{
		foreach( $this->items as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @param int $flag
	 * @return $this
	 */
	function value( ?int $lower, ?int $upper, int $flag = 0 ) : static
	{
		foreach( $this->items as $item ) {
			$item->value( $lower, $upper, $flag );
		}

		return $this;
	}

	/**
	 * @param int $value
	 * @return $this
	 */
	function modulo( int $value ) : static
	{
		foreach( $this->items as $item ) {
			$item->modulo( $value );
		}

		return $this;
	}
}
