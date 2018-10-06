<?php

namespace Kucbel\Scalar\Validator;

use Nette\SmartObject;

abstract class Validator implements ValidatorInterface
{
	use SmartObject;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @param int $code
	 * @param array $values
	 */
	protected function error( int $code, array $values = null )
	{
		throw new ValidatorException( $this->name, $code, $values );
	}

	/**
	 * @return mixed
	 */
	function fetch()
	{
		return $this->value;
	}
}