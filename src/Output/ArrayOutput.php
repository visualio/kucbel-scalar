<?php

namespace Kucbel\Scalar\Output;

use ArrayAccess;
use Nette\SmartObject;

class ArrayOutput implements OutputInterface
{
	use SmartObject;

	/**
	 * @var iterable | array | null
	 */
	protected $values;

	/**
	 * ArrayOutput constructor.
	 *
	 * @param ArrayAccess | null $values
	 */
	function __construct( ArrayAccess $values = null )
	{
		$this->values = $values;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, mixed $value )
	{
		$this->values[ $name ] = $value;
	}

	/**
	 * @return ArrayAccess | array | null
	 */
	function fetch() : ArrayAccess | array | null
	{
		return $this->values;
	}
}
