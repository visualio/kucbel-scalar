<?php

namespace Kucbel\Scalar\Input;

class DirectInput extends StrictInput
{
	/**
	 * @var array
	 */
	private $array;

	/**
	 * @var string | null
	 */
	private $alias;

	/**
	 * DirectInput constructor.
	 *
	 * @param array $array
	 * @param string $alias
	 */
	function __construct( array $array, string $alias = null )
	{
		$this->array = $array;
		$this->alias = self::suffix( $alias );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		return $this->search( $this->array, $name, $null );
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->alias . $name;
	}
}