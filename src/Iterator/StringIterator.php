<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\StringValidator;
use Nette\InvalidArgumentException;

/**
 * Class StringIterator
 *
 * @method StringValidator item( int $index )
 * @method StringValidator first()
 * @method StringValidator last()
 * @method StringValidator current()
 *
 * @method string[] fetch()
 */
class StringIterator extends ScalarIterator
{
	/**
	 * StringIterator constructor.
	 *
	 * @param string $name
	 * @param StringValidator ...$items
	 */
	function __construct( string $name, StringValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @param string ...$values
	 * @return $this
	 */
	function exist( string ...$values )
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
	 * @param string ...$values
	 * @return $this
	 */
	function equal( string ...$values )
	{
		foreach( $this->items as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function char( ?int $lower, ?int $upper )
	{
		foreach( $this->items as $item ) {
			$item->char( $lower, $upper );
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function line( ?int $lower, ?int $upper )
	{
		foreach( $this->items as $item ) {
			$item->line( $lower, $upper );
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function class( string $type )
	{
		foreach( $this->items as $item ) {
			$item->class( $type );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function email()
	{
		foreach( $this->items as $item ) {
			$item->email();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url()
	{
		foreach( $this->items as $item ) {
			$item->url();
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = true )
	{
		foreach( $this->items as $item ) {
			$item->file( $real );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function folder( bool $real = true )
	{
		foreach( $this->items as $item ) {
			$item->folder( $real );
		}

		return $this;
	}
}
