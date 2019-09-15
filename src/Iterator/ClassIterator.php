<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\ClassValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\InvalidArgumentException;

/**
 * Class ClassIterator
 *
 * @method ClassValidator item( int $index )
 * @method ClassValidator first()
 * @method ClassValidator last()
 * @method ClassValidator current()
 *
 * @method string[] fetch()
 */
class ClassIterator extends ScalarIterator
{
	/**
	 * ClassIterator constructor.
	 *
	 * @param string $name
	 * @param ClassValidator ...$list
	 */
	function __construct( string $name, ClassValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param string ...$types
	 * @return $this
	 */
	function exist( string ...$types )
	{
		if( !$types ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( array_diff( $types, $this->fetch() )) {
			throw new ValidatorException( $this->name, Error::ARR_EXIST, [ 'list' => $types ]);
		}

		return $this;
	}

	/**
	 * @param string ...$types
	 * @return $this
	 */
	function equal( string ...$types )
	{
		foreach( $this->list as $item ) {
			$item->equal( ...$types );
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function extend( string $type )
	{
		foreach( $this->list as $item ) {
			$item->extend( $type );
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function implement( string $type )
	{
		foreach( $this->list as $item ) {
			$item->implement( $type );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function interface()
	{
		foreach( $this->list as $item ) {
			$item->interface();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function abstract()
	{
		foreach( $this->list as $item ) {
			$item->abstract();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function concrete()
	{
		foreach( $this->list as $item ) {
			$item->concrete();
		}

		return $this;
	}
}
