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