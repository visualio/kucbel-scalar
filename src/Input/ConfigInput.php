<?php

namespace Kucbel\Scalar\Input;

class ConfigInput extends StrictInput
{
	/**
	 * @var string | null
	 */
	private $section;

	/**
	 * ConfigInput constructor.
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
		$value = ini_get( $this->alias( $name ));

		return $value === '' ? $null : $value;
	}

	/**
	 * @param string|null $section
	 * @return ConfigInput
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
		return "{$this->section}$name";
	}
}