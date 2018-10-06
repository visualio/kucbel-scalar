<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ValidatorException;
use Kucbel\Scalar\Validator\ValidatorInterface;
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
	 * @param int $code
	 * @param array $values
	 */
	protected function error( int $code, array $values = null )
	{
		throw new ValidatorException( $this->name, $code, $values );
	}

	/**
	 * @param string $method
	 * @param mixed ...$arguments
	 * @return $this
	 */
	protected function each( string $method, ...$arguments )
	{
		foreach( $this->list as $item ) {
			$item->$method( ...$arguments );
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
			$this->error( Error::ARR_LEN_GTE, ['min' => $index + 1 ]);
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