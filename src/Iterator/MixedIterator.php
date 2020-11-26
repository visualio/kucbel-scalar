<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\MixedValidator;

/**
 * Class MixedIterator
 *
 * @method MixedValidator item( int $index )
 * @method MixedValidator first()
 * @method MixedValidator last()
 * @method MixedValidator current()
 */
class MixedIterator extends Iterator
{
	/**
	 * MixedIterator constructor.
	 *
	 * @param string $name
	 * @param MixedValidator ...$items
	 */
	function __construct( string $name, MixedValidator ...$items )
	{
		$this->name = $name;
		$this->items = $items;
	}

	/**
	 * @return BoolIterator
	 */
	function bool()
	{
		$items = [];

		foreach( $this->items as $item ) {
			$items[] = $item->bool();
		}

		return new BoolIterator( $this->name, ...$items );
	}

	/**
	 * @return FloatIterator
	 */
	function float()
	{
		$items = [];

		foreach( $this->items as $item ) {
			$items[] = $item->float();
		}

		return new FloatIterator( $this->name, ...$items );
	}

	/**
	 * @return IntegerIterator
	 */
	function integer()
	{
		$items = [];

		foreach( $this->items as $item ) {
			$items[] = $item->integer();
		}

		return new IntegerIterator( $this->name, ...$items );
	}

	/**
	 * @return StringIterator
	 */
	function string()
	{
		$items = [];

		foreach( $this->items as $item ) {
			$items[] = $item->string();
		}

		return new StringIterator( $this->name, ...$items );
	}

	/**
	 * @return DateIterator
	 */
	function date()
	{
		$items = [];

		foreach( $this->items as $item ) {
			$items[] = $item->date();
		}

		return new DateIterator( $this->name, ...$items );
	}
}
