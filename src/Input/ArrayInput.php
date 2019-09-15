<?php

namespace Kucbel\Scalar\Input;

use ArrayAccess;
use Nette\Utils\ArrayHash;

class ArrayInput extends Input implements DetectInterface
{
	/**
	 * @var ArrayAccess
	 */
	protected $array;

	/**
	 * ArrayInput constructor.
	 *
	 * @param ArrayAccess $array
	 */
	function __construct( ArrayAccess $array )
	{
		$this->array = $array;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->array[ $name ] ?? null;
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool
	{
		return $source instanceof ArrayAccess and !$source instanceof ArrayHash;
	}
}
