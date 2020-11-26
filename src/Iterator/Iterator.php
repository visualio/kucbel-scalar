<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Property;
use Kucbel\Scalar\Validator\ValidatorInterface;
use Nette\InvalidArgumentException;

abstract class Iterator extends Property implements IteratorInterface
{
	/**
	 * @var ValidatorInterface[]
	 */
	protected $items;

	/**
	 * @var int
	 */
	private $index = 0;

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function count( ?int $lower, ?int $upper )
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$check = count( $this->items );
		$match = true;

		if( $lower !== null and $lower !== 0 ) {
			if( $lower < 0 ) {
				throw new InvalidArgumentException("Enter a positive lower limit.");
			}

			if( $check < $lower ) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 0 ) {
				throw new InvalidArgumentException("Enter a positive upper limit.");
			}

			if( $check > $upper ) {
				$match = false;
			}
		}

		if( !$match ) {
			if( $lower === $upper ) {
				$text = "exactly \$lower";
			} elseif( $upper === null ) {
				$text = "at least \$lower";
			} elseif( $lower === null ) {
				$text = "at most \$upper";
			} else {
				$text = "between \$lower and \$upper";
			}

			$this->error("Parameter \$name must contain {$text} values.", Error::ARR_COUNT, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	function fetch()
	{
		$values = [];

		foreach( $this->items as $item ) {
			$values[] = $item->fetch();
		}

		return $values;
	}

	/**
	 * @param int $index
	 * @return ValidatorInterface
	 */
	function item( int $index )
	{
		$item = $this->items[ $index ] ?? null;

		if( $item === null ) {
			$this->error("Parameter \$name must contain at least \$lower values.", Error::ARR_COUNT, ['lower' => $index + 1 ]);
		}

		return $item;
	}

	/**
	 * @return ValidatorInterface
	 */
	function first()
	{
		return $this->item( 0 );
	}

	/**
	 * @return ValidatorInterface
	 */
	function last()
	{
		return $this->item( $this->items ? count( $this->items ) - 1 : 0 );
	}

	/**
	 * @return ValidatorInterface
	 */
	function current()
	{
		return $this->item( $this->index );
	}

	/**
	 * @return int
	 */
	function key()
	{
		return $this->index;
	}

	/**
	 * @return bool
	 */
	function valid()
	{
		return isset( $this->items[ $this->index ] );
	}

	/**
	 * @return void
	 */
	function next()
	{
		$this->index++;
	}

	/**
	 * @return void
	 */
	function rewind()
	{
		$this->index = 0;
	}
}
