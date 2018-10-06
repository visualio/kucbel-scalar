<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
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
	 * @param int $limit
	 * @return $this
	 */
	function min( int $limit )
	{
		$count = count( $this->list );

		if( $count < $limit ) {
			$this->error( Error::ARR_LEN_GTE, ['min' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function max( int $limit )
	{
		$count = count( $this->list );

		if( $count > $limit ) {
			$this->error( Error::ARR_LEN_LTE, ['max' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function count( int $min, int $max = null )
	{
		$count = count( $this->list );

		if( $min === $max or $max === null ) {
			$equal = true;
		} else {
			$equal = false;
		}

		if( $equal and $count !== $min ) {
			$this->error( Error::ARR_LEN_EQ, ['len' => $min ]);
		} elseif( !$equal and ( $count < $min or $count > $max )) {
			$this->error( Error::ARR_LEN_RNG, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @return BoolIterator
	 */
	function bool()
	{
		$items = [];

		foreach( $this->list as $value ) {
			$items[] = $value->bool();
		}

		return new BoolIterator( $this->name, ...$items );
	}

	/**
	 * @return FloatIterator
	 */
	function float()
	{
		$items = [];

		foreach( $this->list as $value ) {
			$items[] = $value->float();
		}

		return new FloatIterator( $this->name, ...$items );
	}

	/**
	 * @return IntegerIterator
	 */
	function integer()
	{
		$items = [];

		foreach( $this->list as $value ) {
			$items[] = $value->integer();
		}

		return new IntegerIterator( $this->name, ...$items );
	}

	/**
	 * @return StringIterator
	 */
	function string()
	{
		$items = [];

		foreach( $this->list as $value ) {
			$items[] = $value->string();
		}

		return new StringIterator( $this->name, ...$items );
	}

	/**
	 * @return DateIterator
	 */
	function date()
	{
		$items = [];

		foreach( $this->list as $value ) {
			$items[] = $value->date();
		}

		return new DateIterator( $this->name, ...$items );
	}
}