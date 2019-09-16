<?php

namespace Kucbel\Scalar\Validator;

use DateTimeInterface;
use Kucbel\Scalar\Error;
use Kucbel\Scalar\Filter\FilterInterface;
use Kucbel\Scalar\Iterator\MixedIterator;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use stdClass;
use Throwable;
use Traversable;

class MixedValidator extends Validator
{
	/**
	 * MixedValidator constructor.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	function __construct( string $name, $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param FilterInterface $filter
	 * @return $this
	 */
	function clear( FilterInterface $filter )
	{
		$this->value = $filter->clear( $this->value );

		return $this;
	}

	/**
	 * @param mixed $value
	 * @return VoidValidator | $this
	 */
	function optional( $value = null )
	{
		if( $this->value !== null ) {
			return $this;
		} elseif( $value !== null ) {
			$this->value = $value;

			return $this;
		} else {
			return new VoidValidator( $this->name );
		}
	}

	/**
	 * @return BoolValidator
	 */
	function bool()
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $value === 1 or $value === 1. or $value === 0. or $value === 0 ) {
			$type = 'bool';
			$value = (bool) $value;
		} elseif( $type === 'str' and $match = Strings::match( $value, '~^(true|on|yes|y|1)$|^(false|off|no|n|0)$~i')) {
			$type = 'bool';
			$value = (bool) $match[1];
		}

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'bool') {
			throw new ValidatorException( $this->name, Error::TYPE_BOOL );
		}

		return new BoolValidator( $this->name, $value );
	}

	/**
	 * @return FloatValidator
	 */
	function float()
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === 'str' and $match = Strings::match( $value, '~^[+-]?([.][0-9]+|[0-9]+[.]?[0-9]*)([Ee][+-]?[0-9]{1,2})?$~') and strlen( Strings::replace( $match[1], '~^[0.]+|[0.]+$|[.]~', '')) <= 14 ) {
			$type = 'dec';
			$value = (float) $value;
		} elseif( $type === 'date') {
			$type = 'dec';
			$value = (float) $value->format('U');
		}

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'dec' and $type !== 'int') {
			throw new ValidatorException( $this->name, Error::TYPE_FLOAT );
		}

		return new FloatValidator( $this->name, $value );
	}

	/**
	 * @return IntegerValidator
	 */
	function integer()
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === 'bool') {
			$type = 'int';
			$value = (int) $value;
		} elseif( $type === 'str' and Strings::match( $value, '~^[+-]?[0-9]+[.]?0*$~') and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $value;
		} elseif( $type === 'dec' and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $value;
		} elseif( $type === 'date' and ( $stamp = $value->format('U')) <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $stamp;
		}

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'int') {
			throw new ValidatorException( $this->name, Error::TYPE_INTEGER );
		}

		return new IntegerValidator( $this->name, $value );
	}

	/**
	 * @return StringValidator
	 */
	function string()
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === 'date') {
			$type = 'str';
			$value = $value->format( DATE_ATOM );
		}

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'str' and $type !== 'int' and $type !== 'dec') {
			throw new ValidatorException( $this->name, Error::TYPE_STRING );
		}

		return new StringValidator( $this->name, $value );
	}

	/**
	 * @return DateValidator
	 */
	function date()
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === 'dec' and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $value;
		}

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'date' and $type !== 'str' and $type !== 'int') {
			throw new ValidatorException( $this->name, Error::TYPE_DATE );
		}

		try {
			$value = DateTime::from( $value );
		} catch( Throwable $ex ) {
			throw new ValidatorException( $this->name, Error::TYPE_DATE );
		}

		return new DateValidator( $this->name, $value );
	}

	/**
	 * @return MixedIterator
	 */
	function array()
	{
		$type = self::detect( $this->value );

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		}

		$hint = strpos( $this->name, '.');
		$list = [];

		if( $type === 'arr') {
			foreach( $this->value as $index => $value ) {
				$name = $hint ? "{$this->name}.{$index}" : "{$this->name}[{$index}]";

				$list[] = new MixedValidator( $name, $value );
			}
		} else {
			$list[] = $this;
		}

		return new MixedIterator( $this->name, ...$list );
	}

	/**
	 * @return MixedIterator
	 */
	function index()
	{
		$type = self::detect( $this->value );

		if( $type === 'null') {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== 'arr') {
			throw new ValidatorException( $this->name, Error::TYPE_ARRAY );
		}

		$hint = strpos( $this->name, '.');
		$list = [];

		foreach( $this->value as $index => $value ) {
			$name = $hint ? "{$this->name}.{$index}" : "{$this->name}[{$index}]";

			$list[] = new MixedValidator( $name, $index );
		}

		return new MixedIterator( $this->name, ...$list );
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	static function detect( $value ) : string
	{
		switch( gettype( $value )) {
			case 'NULL':
				return 'null';
			case 'boolean':
				return 'bool';
			case 'integer':
				return 'int';
			case 'double':
				return is_finite( $value ) ? 'dec' : 'wtf';
			case 'string':
				return 'str';
			case 'array':
				return 'arr';
			case 'resource':
			case 'unknown type':
				return 'wtf';
		}

		switch( true ) {
			case $value instanceof DateTimeInterface:
				return 'date';
			case $value instanceof Traversable:
			case $value instanceof stdClass:
				return 'arr';
			default:
				return 'obj';
		}
	}
}
