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

	/**
	 * @param mixed $value
	 * @return array
	 */
	static function range( $value ) : array
	{
		if( is_array( $value )) {
			return [ key( $value ), current( $value ) ];
		} else {
			return [ null, $value ];
		}
	}
}
