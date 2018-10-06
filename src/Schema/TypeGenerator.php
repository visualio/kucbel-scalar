<?php

namespace Kucbel\Scalar\Schema;

use Error;
use Nette\PhpGenerator\Closure;
use Nette\SmartObject;
use Nette\Utils\Strings;

class TypeGenerator
{
	use SmartObject;

	/**
	 * @param array ...$methods
	 * @return Closure
	 */
	function create( array ...$methods ) : Closure
	{
		$closure = new Closure;
		$closure->addParameter('mixed');
		$closure->addBody('return $mixed');

		foreach( $methods as $arguments ) {
			$method = array_shift( $arguments );

			$closure->addBody("->{$method}(...?)", [ $arguments ]);
		}

		$closure->addBody('->fetch();');

		return $closure;
	}

	/**
	 * @param array ...$methods
	 * @return string
	 */
	function compress( array ...$methods ) : string
	{
		$closure = $this->create( ...$methods );

		return Strings::replace("return $closure;", '~[\n][\t]*(return|->|;|})~', '$1');
	}

	/**
	 * @param string $code
	 * @return callable
	 * @throws Error
	 */
	function compile( string $code ) : callable
	{
		return eval( $code );
	}
}