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
	 * @param array ...$rules
	 * @return Closure
	 */
	function create( array ...$rules ) : Closure
	{
		$closure = new Closure;
		$closure->addParameter('mixed');
		$closure->addBody('return $mixed');

		foreach( $rules as $rule ) {
			$method = array_shift( $rule );

			$closure->addBody("->{$method}(...?)", [ $rule ]);
		}

		$closure->addBody('->fetch();');

		return $closure;
	}

	/**
	 * @param array ...$rules
	 * @return string
	 */
	function compress( array ...$rules ) : string
	{
		$closure = $this->create( ...$rules );

		return Strings::replace("return $closure;", '~\n\t*(return|->|;|})~', '$1');
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
