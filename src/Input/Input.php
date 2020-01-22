<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

abstract class Input implements InputInterface
{
	use SmartObject;

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		return new MixedValidator( $this->alias( $name ), $this->get( $name ));
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->get( $name ) !== null;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $name;
	}

	/**
	 * @param string | null $name
	 * @return string | null
	 */
	static function suffix( ?string $name ) : ?string
	{
		if( $name === null or $name === '' ) {
			return null;
		} elseif( $name[-1] === '.') {
			return $name;
		} else {
			return "{$name}.";
		}
	}
}
