<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class CallType implements TypeInterface
{
	use SmartObject;

	/**
	 * @var callable
	 */
	private $test;

	/**
	 * CallType constructor.
	 *
	 * @param callable $test
	 */
	function __construct( callable $test )
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
