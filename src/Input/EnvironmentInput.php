<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Output\OutputException;
use Kucbel\Scalar\Output\OutputInterface;

class EnvironmentInput extends StrictInput implements OutputInterface
{
	use InputSection;

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
		$value = getenv( $this->format( $this->section . $name ));

		return $value !== false ? $value : $null;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$name = $this->format( $this->section . $name );

		if( $value === null ) {
			$done = putenv( $name );
		} elseif( is_bool( $value )) {
			$value = $value ? 1 : 0;
			$done = putenv("{$name}={$value}");
		} elseif( is_scalar( $value )) {
			$done = putenv("{$name}={$value}");
		} else {
			$done = false;
		}

		if( $done === false ) {
			throw new OutputException("Unable to modify environment value.");
		}
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->format( $this->section . $name );
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function format( string $name ) : string
	{
		return strtoupper( str_replace('.', '_', $name ));
	}
}
