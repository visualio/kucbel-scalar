<?php

namespace Kucbel\Scalar\Input;

class EnvironmentInput extends StrictInput
{
	/**
	 * @var string | null
	 */
	private $section;

	/**
	 * EnvironmentInput constructor.
	 *
	 * @param string $section
	 */
	function __construct( string $section = null )
	{
		$this->section = self::suffix( $section );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		$value = getenv( $this->alias( $name ));

		return $value !== false ? $value : $null;
	}

	/**
	 * @param string $section
	 * @return EnvironmentInput
	 */
	function section( ?string $section ) : self
	{
		$that = clone $this;
		$that->section = self::suffix( $section );

		return $that;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return strtoupper( str_replace('.', '_', "{$this->section}$name"));
	}
}