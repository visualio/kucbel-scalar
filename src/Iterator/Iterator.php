<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ValidatorException;
use Kucbel\Scalar\Validator\ValidatorInterface;
use Nette\InvalidArgumentException;
use Nette\SmartObject;

abstract class Iterator implements IteratorInterface
{
	use SmartObject;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var ValidatorInterface[]
	 */
	protected $list;

	/**
	 * @var int
	 */
	private $index = 0;

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function count( ?int $min, ?int $max )
	{
		if(( $min !== null and $min < 0 ) or ( $max !== null and $max < 0 )) {
			throw new InvalidArgumentException("Enter a positive count limit.");
		} elseif( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$num = count( $this->list );

		if(( $min !== null and $num < $min ) or ( $max !== null and $num > $max )) {
			throw new ValidatorException( $this->name, Error::ARR_COUNT, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	function fetch()
	{
		$values = [];

		foreach( $this->list as $item ) {
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
		$value = $this->list[ $index ] ?? null;

		if( $value === null ) {
			throw new ValidatorException( $this->name, Error::ARR_COUNT, ['min' => $index + 1, 'max' => null ]);
		}

		return $value;
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
		return $this->item( $this->list ? count( $this->list ) - 1 : 0 );
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
		return isset( $this->list[ $this->index ] );
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
