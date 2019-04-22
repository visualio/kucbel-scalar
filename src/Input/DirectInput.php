<?php

namespace Kucbel\Scalar\Input;

class DirectInput extends StrictInput
{
	/**
	 * @var array
	 */
	private $values;

	/**
	 * @var string | null
	 */
	private $alias;

	/**
	 * DirectInput constructor.
	 *
	 * @param array $values
	 * @param string $alias
	 */
	function __construct( array $values, string $alias = null )
	{
		$this->values = $values;
		$this->alias = self::suffix( $alias );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		return $this->search( $this->values, $name, $null );
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