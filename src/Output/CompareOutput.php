<?php

namespace Kucbel\Scalar\Output;

use ArrayAccess;
use DateTimeInterface;
use Kucbel\Scalar\Input\InputInterface;

class CompareOutput extends Output
{
	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * @var iterable | null
	 */
	private $values;

	/**
	 * CompareOutput constructor.
	 *
	 * @param InputInterface $input
	 * @param ArrayAccess $values
	 */
	function __construct( InputInterface $input, ArrayAccess $values = null )
	{
		$this->input = $input;
		$this->values = $values;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$exist = $this->input->get( $name );

		if( $exist instanceof DateTimeInterface and $value instanceof DateTimeInterface ) {
			$strict = false;
		} else {
			$strict = true;
		}

		if(( $strict and $exist !== $value ) or ( !$strict and $exist != $value )) {
			$this->values[ $name ] = $value;
		}
	}

	/**
	 * @return iterable | array | null
	 */
	function fetch() : ?iterable
	{
		return $this->values;
	}
}