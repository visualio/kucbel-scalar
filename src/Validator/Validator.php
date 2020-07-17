<?php

namespace Kucbel\Scalar\Validator;

use Nette\SmartObject;

abstract class Validator implements ValidatorInterface
{
	use SmartObject;

	const
		EXCL_MIN	= 0b1,
		EXCL_MAX	= 0b10,
		EXCL_BOTH	= 0b11;

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
