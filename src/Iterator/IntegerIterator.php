<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Error;
use Kucbel\Scalar\Validator\IntegerValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\InvalidArgumentException;

/**
 * Class IntegerIterator
 *
 * @method IntegerValidator item( int $index )
 * @method IntegerValidator first()
 * @method IntegerValidator last()
 * @method IntegerValidator current()
 *
 * @method int[] fetch()
 */
class IntegerIterator extends NumericIterator
{
	/**
	 * IntegerIterator constructor.
	 *
	 * @param string $name
	 * @param IntegerValidator ...$list
	 */
	function __construct( string $name, IntegerValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param int  ...$values
	 * @return $this
	 */
	function exist( int ...$values )
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
	 * @param int ...$values
	 * @return $this
	 */
	function equal( int ...$values )
	{
		foreach( $this->list as $item ) {
			$item->equal( ...$values );
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @param int $opt
	 * @return $this
	 */
	function value( ?int $min, ?int $max, int $opt = 0 )
	{
		foreach( $this->list as $item ) {
			$item->value( $min, $max, $opt );
		}

		return $this;
	}

	/**
	 * @param int $div
	 * @return $this
	 */
	function modulo( int $div )
	{
		foreach( $this->list as $item ) {
			$item->modulo( $div );
		}

		return $this;
	}

	/**
	 * @param int|int[] $digit
	 * @return $this
	 */
	function length( $digit )
	{
		$digit = IntegerValidator::range( $digit );

		foreach( $this->list as $item ) {
			$item->digit( ...$digit );
		}

		return $this;
	}
}
