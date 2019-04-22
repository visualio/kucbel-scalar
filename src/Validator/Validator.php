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
	 * @return mixed
	 */
	function fetch()
	{
		return $this->value;
	}
}