<?php

namespace Kucbel\Scalar\Schema;

use DateTimeInterface;
use Error;
use Nette\InvalidArgumentException;
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

		foreach( $rules as $parts ) {
			$method = array_shift( $parts );

			foreach( $parts as $part ) {
				if( $part instanceof DateTimeInterface ) {
					throw new InvalidArgumentException("Unable to compress datetime object.");
				}
			}

			$closure->addBody("->{$method}(...?)", [ $parts ]);
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
