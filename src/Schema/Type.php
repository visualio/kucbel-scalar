<?php

namespace Kucbel\Scalar\Schema;

use Closure;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class Type implements TypeInterface
{
	use SmartObject;

	/**
	 * @var Closure
	 */
	private $test;

	/**
	 * Type constructor.
	 *
	 * @param Closure $test
	 */
	function __construct( Closure $test )
	{
		$this->test = $test;
	}

	/**
	 * @param MixedValidator $column
	 * @return mixed
	 */
	function fetch( MixedValidator $column )
	{
		return call_user_func( $this->test, $column );
	}
}