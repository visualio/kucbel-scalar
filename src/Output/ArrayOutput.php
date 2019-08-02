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
	 * @param ArrayAccess $values
	 */
	function __construct( ArrayAccess $values = null )
	{
		$this->values = $values;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$this->values[ $name ] = $value;
	}

	/**
	 * @return iterable | array | null
	 */
	function fetch() : ?iterable
	{
		return $this->values;
	}
}
