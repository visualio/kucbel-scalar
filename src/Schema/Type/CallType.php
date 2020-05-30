<?php

namespace Kucbel\Scalar\Schema\Type;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class CallType implements TypeInterface
{
	use SmartObject;

	/**
	 * @var callable
	 */
	protected $callback;

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
	 * @param MixedValidator $value
	 * @return mixed
	 */
	function fetch( MixedValidator $value )
	{
		return ( $this->callback )( $value );
	}
}
