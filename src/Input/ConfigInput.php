<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Output\OutputException;
use Kucbel\Scalar\Output\OutputInterface;

class ConfigInput extends StrictInput implements OutputInterface
{
	use SectionMethod;

	/**
	 * ConfigInput constructor.
	 *
	 * @param string | null $section
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
	function get( string $name, mixed $null = null ) : mixed
	{
		$value = ini_get( $this->section . $name );

		return $value === '' ? $null : $value;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, mixed $value )
	{
		$name = $this->section . $name;

		if( $value === null ) {
			$done = ini_set( $name, '');
		} elseif( is_bool( $value )) {
			$done = ini_set( $name, $value ? 1 : 0 );
		} elseif( is_scalar( $value )) {
			$done = ini_set( $name, $value );
		} else {
			$done = false;
		}

		if( $done === false ) {
			throw new OutputException("Unable to modify configuration value.");
		}
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->section . $name;
	}
}
