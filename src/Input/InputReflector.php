<?php

namespace Kucbel\Scalar\Input;

use Nette\SmartObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;

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
	 * @param ReflectionClass ...$ignore
	 */
	function __construct( ReflectionClass ...$ignore )
	{
		if( $ignore ) {
			$methods = $this->getMethods( ...$ignore );

			if( $methods ) {
				array_push( $this->ignore, ...$methods );
			}
		}
	}

	/**
	 * @param ReflectionClass $class
	 * @return string
	 * @throws ReflectionException
	 */
	function getArgument( ReflectionClass $class ) : string
	{
		if( !$class->isInstantiable() ) {
			throw new InputException("Class '{$class->getName()}' must be instantiable.");
		}

		$method = $class->hasMethod('__construct') ? $class->getMethod('__construct') : null;

		if( !$method instanceof ReflectionMethod ) {
			throw new InputException("Class '{$class->getName()}' must have a constructor.");
		}

		$param = $method->getParameters();
		$param = reset( $param );

		if( !$param instanceof ReflectionParameter ) {
			throw new InputException("Method '{$class->getName()}::{$method->getName()}()' must have at least one parameter.");
		}

		$type = $param->hasType() ? $param->getType() : null;

		if( !$type instanceof ReflectionType ) {
			throw new InputException("Parameter '{$class->getName()}::\${$param->getName()}' must have type hint.");
		} elseif( $type->isBuiltin() ) {
			throw new InputException("Parameter '{$class->getName()}::\${$param->getName()}' hint must be either class or interface.");
		}

		$hint = new ReflectionClass( $type->getName() );

		if( $hint->isTrait() ) {
			throw new InputException("Parameter '{$class->getName()}::\${$param->getName()}' hint must be either class or interface.");
		}

		return $hint->getName();
	}

	/**
	 * @param ReflectionClass ...$classes
	 * @return array
	 */
	function getMethods( ReflectionClass ...$classes ) : array
	{
		$names = [];

		foreach( $classes as $class ) {
			foreach( $class->getMethods() as $method ) {
				if( $method->isPublic() and !$method->isStatic() ) {
					$names[] = $method->getName();
				}
			}
		}

		$names = array_unique( $names );
		$names = array_diff( $names, $this->ignore );

		sort( $names );

		return $names;
	}
}
