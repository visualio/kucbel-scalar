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
	const
		NULL	= 1,
		BOOL	= 2,
		INT		= 3,
		FLO		= 4,
		STR		= 5,
		DATE	= 6,
		OBJ		= 7,
		ARR		= 8,
		POO		= 9;

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
		$this->value = $filter->value( $this->value );

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
			$type = self::BOOL;
			$value = (bool) $value;
		} elseif( $type === self::STR and $match = Strings::match( $value, '~^(true|on|yes|y|1)$|^(false|off|no|n|0)$~i')) {
			$type = self::BOOL;
			$value = (bool) $match[1];
		}

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::BOOL ) {
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

		if( $type === self::STR and $match = Strings::match( $value, '~^[+-]?([.][0-9]+|[0-9]+[.]?[0-9]*)([Ee][+-]?[0-9]{1,2})?$~') and strlen( Strings::replace( $match[1], '~^[0.]+|[0.]+$|[.]~', '')) <= 14 ) {
			$type = self::FLO;
			$value = (float) $value;
		} elseif( $type === self::DATE ) {
			$type = self::FLO;
			$value = (float) $value->format('U');
		}

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::FLO and $type !== self::INT ) {
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

		if( $type === self::BOOL ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::STR and Strings::match( $value, '~^[+-]?[0-9]+[.]?0*$~') and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::FLO and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::DATE and ( $stamp = $value->format('U')) <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $stamp;
		}

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::INT ) {
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

		if( $type === self::DATE ) {
			$type = self::STR;
			$value = $value->format( DATE_ATOM );
		}

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::STR and $type !== self::INT and $type !== self::FLO ) {
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

		if( $type === self::FLO and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		}

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::DATE and $type !== self::STR and $type !== self::INT ) {
			throw new ValidatorException( $this->name, Error::TYPE_DATE );
		}

		try {
			$value = DateTime::from( $value );
		} catch( Throwable $error ) {
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

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		}

		$hint = strpos( $this->name, '.');
		$list = [];

		if( $type === self::ARR ) {
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

		if( $type === self::NULL ) {
			throw new ValidatorException( $this->name, Error::TYPE_NULL );
		} elseif( $type !== self::ARR ) {
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
	 * @return int
	 */
	static function detect( $value ) : int
	{
		switch( gettype( $value )) {
			case 'NULL':
				return self::NULL;
			case 'boolean':
				return self::BOOL;
			case 'integer':
				return self::INT;
			case 'double':
				return is_finite( $value ) ? self::FLO : self::POO;
			case 'string':
				return self::STR;
			case 'array':
				return self::ARR;
			case 'resource':
			case 'unknown type':
				return self::POO;
		}

		switch( true ) {
			case $value instanceof DateTimeInterface:
				return self::DATE;
			case $value instanceof Traversable:
			case $value instanceof stdClass:
				return self::ARR;
			default:
				return self::OBJ;
		}
	}
}
