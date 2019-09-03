<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\MixedValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\InvalidArgumentException;

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
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function count( ?int $min, ?int $max = 0 )
	{
		if( $min !== null and $max !== null and $min > $max ) {
			[ $min, $max ] = [ $max, $min ];
		}

		if( $min === 0 ) {
			$min = null;
		}

		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		$num = count( $this->list );

		if( $min !== null ) {
			if( $min < 0 ) {
				throw new InvalidArgumentException("Enter positive count limit.");
			}

			if( $num < $min ) {
				throw new ValidatorException( $this->name, Error::ARR_COUNT, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 0 ) {
				throw new InvalidArgumentException("Enter positive count limit.");
			}

			if( $num > $max ) {
				throw new ValidatorException( $this->name, Error::ARR_COUNT, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
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
