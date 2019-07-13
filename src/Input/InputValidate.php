<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;

trait InputValidate
{
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
	protected static function validate( $parent )
	{
		$hash = spl_object_hash( $parent );

		static $index;

		return $index[ $hash ] ?? $index[ $hash ] = new ExistValidator( ...static::$ignore );
	}

	/**
	 * @param string ...$names
	 */
	static function ignore( string ...$names )
	{
		foreach( $names as $name ) {
			static::$ignore[] = $name;
		}
	}

	/**
	 * @throws ValidatorException
	 */
	abstract function match() : void;
}
