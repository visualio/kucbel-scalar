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
	private $callback;

	/**
	 * CallType constructor.
	 *
	 * @param callable $callback
	 */
	function __construct( callable $callback )
	{
		$this->callback = $callback;
	}

	/**
	 * @param MixedValidator $column
	 * @return mixed
	 */
	function fetch( MixedValidator $column )
	{
		return call_user_func( $this->callback, $column );
	}
}
