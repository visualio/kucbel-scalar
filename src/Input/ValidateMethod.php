<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;

trait ValidateMethod
{
	/**
	 * @var ExistValidator[]
	 */
	protected static $parent = [];

	/**
	 * @var string[]
	 */
	protected static $ignore = [];

	/**
	 * @var ExistValidator
	 */
	protected $validator;

	/**
	 * @param object $parent
	 * @return ExistValidator
	 */
	protected static function validate( object $parent )
	{
		$hash = spl_object_hash( $parent );

		return self::$parent[ $hash ] ?? self::$parent[ $hash ] = new ExistValidator( ...self::$ignore );
	}

	/**
	 * @param string ...$names
	 */
	static function ignore( string ...$names )
	{
		foreach( $names as $name ) {
			self::$ignore[] = $name;
		}
	}

	/**
	 * @throws ValidatorException
	 */
	abstract function match() : void;
}
