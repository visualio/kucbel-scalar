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
	 * @param MixedValidator ...$list
	 */
	function __construct( string $name, MixedValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @return BoolIterator
	 */
	function bool()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->bool();
		}

		return new BoolIterator( $this->name, ...$list );
	}

	/**
	 * @return FloatIterator
	 */
	function float()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->float();
		}

		return new FloatIterator( $this->name, ...$list );
	}

	/**
	 * @return IntegerIterator
	 */
	function integer()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->integer();
		}

		return new IntegerIterator( $this->name, ...$list );
	}

	/**
	 * @return StringIterator
	 */
	function string()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->string();
		}

		return new StringIterator( $this->name, ...$list );
	}

	/**
	 * @return ClassIterator
	 */
	function class()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->class();
		}

		return new ClassIterator( $this->name, ...$list );
	}

	/**
	 * @return DateIterator
	 */
	function date()
	{
		$list = [];

		foreach( $this->list as $item ) {
			$list[] = $item->date();
		}

		return new DateIterator( $this->name, ...$list );
	}
}
