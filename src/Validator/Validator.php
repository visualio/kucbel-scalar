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
