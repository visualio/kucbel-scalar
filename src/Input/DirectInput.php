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
	private $section;

	/**
	 * DirectInput constructor.
	 *
	 * @param array $values
	 * @param string $section
	 */
	function __construct( array $values, string $section = null )
	{
		$this->values = $values;
		$this->section = self::suffix( $section );
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
		return "{$this->section}$name";
	}
}