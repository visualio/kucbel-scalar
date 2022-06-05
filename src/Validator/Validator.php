<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Property;

abstract class Validator extends Property implements ValidatorInterface
{
	const
		EXCL_LOWER	= 0b1,
		EXCL_UPPER	= 0b10,
		EXCL_LIMIT	= 0b11;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @return mixed
	 */
	function fetch() : mixed
	{
		return $this->value;
	}
}
