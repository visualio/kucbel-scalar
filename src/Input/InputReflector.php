<?php

namespace Kucbel\Scalar\Input;

use Nette\SmartObject;
use ReflectionClass;

class InputReflector
{
	use SmartObject;

	/**
	 * @var array
	 */
	private $ignore = [
		'__construct',
		'__destruct',
		'__clone',
		'__call',
		'__get',
		'__set',
		'__isset',
		'__unset',
		'__sleep',
		'__wakeup',
		'__invoke',
		'__toString',
		'__debugInfo',
	];

	/**
	 * InputReflector constructor.
	 *
	 * @param ReflectionClass ...$ignores
	 */
	function __construct( ReflectionClass ...$ignores )
	{
		if( $ignores ) {
			$methods = $this->getMethods( ...$ignores );

			foreach( $methods as $method ) {
				$this->ignore[ $method ] = $method;
			}
		}
	}

	/**
	 * @param ReflectionClass ...$classes
	 * @return array
	 */
	function getMethods( ReflectionClass ...$classes ) : array
	{
		$methods = [];

		foreach( $classes as $class ) {
			foreach( $class->getMethods() as $method ) {
				if( $method->isPublic() and !$method->isStatic() ) {
					$name = $method->getName();

					$methods[ $name ] = $name;
				}
			}
		}

		$methods = array_diff_key( $methods, $this->ignore );
		$methods = array_values( $methods );

		sort( $methods );

		return $methods;
	}
}
