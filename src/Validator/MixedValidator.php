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

		if( $type === 'int' and ( $value === 1 or $value === 0 )) {
			$type = 'bool';
			$value = (bool) $value;
		} elseif( $type === 'dec' and ( $value === 1. or $value === 0. )) {
			$type = 'bool';
			$value = (bool) $value;
		} elseif( $type === 'str' and $match = Strings::match( $value, '~^(true|on|yes|y|1)$|^(false|off|no|n|0)$~i')) {
			$type = 'bool';
			$value = (bool) $match[1];
		}

		if( $type === 'null') {
			$this->error( Error::TYPE_NULL );
		} elseif( $type !== 'bool') {
			$this->error( Error::TYPE_BOOL );
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

		if( $type === 'dec' and !is_finite( $value )) {
			$type = 'wtf';
		} elseif( $type === 'str' and $match = Strings::match( $value, '~^[+-]?([.][0-9]+|[0-9]+[.]?[0-9]*)([Ee][+-]?[0-9]{1,2})?$~') and strlen( Strings::replace( $match[1], '~^[0.]+|[0.]+$|[.]~', '')) <= 14 ) {
			$type = 'dec';
			$value = (float) $value;
		} elseif( $type === 'date') {
			$type = 'dec';
			$value = (float) $value->format('U');
		}

		if( $type === 'null') {
			$this->error( Error::TYPE_NULL );
		} elseif( $type !== 'dec' and $type !== 'int') {
			$this->error( Error::TYPE_FLOAT );
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
		} elseif( $type === 'dec' and is_finite( $value ) and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $value;
		} elseif( $type === 'date' and ( $stamp = $value->format('U')) <= PHP_INT_MAX ) {
			$type = 'int';
			$value = (int) $stamp;
		}

		if( $type === 'null') {
			$this->error( Error::TYPE_NULL );
		} elseif( $type !== 'int') {
			$this->error( Error::TYPE_INTEGER );
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
			$this->error( Error::TYPE_NULL );
		} elseif( $type !== 'str' and $type !== 'int' and $type !== 'dec') {
			$this->error( Error::TYPE_STRING );
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

		if( $type === 'null') {
			$this->error( Error::TYPE_NULL );
		} elseif( $type === 'arr' or $type === 'obj') {
			$this->error( Error::TYPE_DATE );
		} elseif( $type === 'dec' and $value !== floor( $value )) {
			$this->error( Error::TYPE_DATE );
		}

		try {
			$value = DateTime::from( $value );
		} catch( Throwable $ex ) {
			$this->error( Error::TYPE_DATE );
		}

		return new DateValidator( $this->name, $value );
	}

	/**
	 * @param bool $keys
	 * @return MixedIterator
	 */
	function array( bool $keys = false )
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === 'null') {
			$this->error( Error::TYPE_NULL );
		} elseif( $type !== 'arr' and $keys ) {
			$this->error( Error::TYPE_ARRAY );
		}

		$items = [];

		if( $type === 'arr') {
			if( strpos( $this->name, '.')) {
				$print = '%s.%s';
			} else {
				$print = '%s[%s]';
			}

			foreach( $value as $index => $each ) {
				$items[] = new MixedValidator( sprintf( $print, $this->name, $index ), $keys ? $index : $each );
			}
		} else {
			$items[] = $this;
		}

		return new MixedIterator( $this->name, ...$items );
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	static protected function detect( $value ) : string 
	{
		switch( gettype( $value )) {
			case 'NULL':
				return 'null';
			case 'boolean':
				return 'bool';
			case 'integer':
				return 'int';
			case 'double':
			case 'float':
				return 'dec';
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