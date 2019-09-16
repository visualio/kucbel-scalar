<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\StringValidator;
use Kucbel\Scalar\Validator\ValidatorException;
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
	 * @param StringValidator ...$list
	 */
	function __construct( string $name, StringValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
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
			throw new ValidatorException( $this->name, Error::ARR_EXIST, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param string ...$values
	 * @return $this
	 */
	function equal( string ...$values )
	{
		foreach( $this->list as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function char( ?int $min, ?int $max = 1 )
	{
		foreach( $this->list as $item ) {
			$item->char( $min, $max );
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function line( ?int $min, ?int $max = 1 )
	{
		foreach( $this->list as $item ) {
			$item->line( $min, $max );
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function class( string $type )
	{
		foreach( $this->list as $item ) {
			$item->class( $type );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function email()
	{
		foreach( $this->list as $item ) {
			$item->email();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url()
	{
		foreach( $this->list as $item ) {
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
		foreach( $this->list as $item ) {
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
		foreach( $this->list as $item ) {
			$item->folder( $real );
		}

		return $this;
	}
}
