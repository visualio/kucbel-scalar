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
	protected const
		NULL	= 1,
		BOOL	= 2,
		INT		= 3,
		DEC		= 4,
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
	function __construct( string $name, mixed $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param FilterInterface $filter
	 * @return $this
	 */
	function clear( FilterInterface $filter ) : static
	{
		$this->value = $filter->clear( $this->value );

		return $this;
	}

	/**
	 * @param mixed $value
	 * @return VoidValidator | $this
	 */
	function optional( mixed $value = null ) : VoidValidator | static
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
	function bool() : BoolValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $value === 1 or $value === 1. or $value === 0. or $value === 0 ) {
			$type = self::BOOL;
			$value = (bool) $value;
		} elseif( $type === self::STR and $match = Strings::match( $value, '~^(true|on|yes|y|1)$|^(false|off|no|n|0)$~iD')) {
			$type = self::BOOL;
			$value = (bool) $match[1];
		}

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::BOOL ) {
			$this->error("Parameter \$name must be a boolean.", Error::TYPE_BOOL );
		}

		return new BoolValidator( $this->name, $value );
	}

	/**
	 * @return FloatValidator
	 */
	function float() : FloatValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === self::STR and $match = Strings::match( $value, '~^[+-]?([.][0-9]+|[0-9]+[.]?[0-9]*)([Ee][+-]?[0-9]{1,2})?$~D') and strlen( Strings::replace( $match[1], '~^[0.]+|[0.]+$|[.]~D')) <= 14 ) {
			$type = self::DEC;
			$value = (float) $value;
		} elseif( $type === self::DATE ) {
			$type = self::DEC;
			$value = (float) $value->format('U');
		}

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::DEC and $type !== self::INT ) {
			$this->error("Parameter \$name must be a float.", Error::TYPE_FLOAT );
		}

		return new FloatValidator( $this->name, $value );
	}

	/**
	 * @return IntegerValidator
	 */
	function integer() : IntegerValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === self::BOOL ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::STR and Strings::match( $value, '~^[+-]?[0-9]+[.]?0*$~D') and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::DEC and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		} elseif( $type === self::DATE and ( $stamp = $value->format('U')) <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $stamp;
		}

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::INT ) {
			$this->error("Parameter \$name must be an integer.", Error::TYPE_INTEGER );
		}

		return new IntegerValidator( $this->name, $value );
	}

	/**
	 * @return DecimalValidator
	 */
	function decimal() : DecimalValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === self::INT ) {
			$value = (string) $value;
		} elseif( $type === self::DEC ) {
			$value = sprintf('%F', $value );
		} elseif( $type === self::STR ) {
			if( !Strings::match( $value, '~^[+-]?([.][0-9]+|[0-9]+[.]?[0-9]*)$~D')) {
				$this->error("Parameter \$name must be a decimal.", Error::TYPE_DECIMAL );
			}
		} elseif( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} else {
			$this->error("Parameter \$name must be a decimal.", Error::TYPE_DECIMAL );
		}

		return new DecimalValidator( $this->name, $value );
	}

	/**
	 * @return StringValidator
	 */
	function string() : StringValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === self::DATE ) {
			$type = self::STR;
			$value = $value->format( DATE_ATOM );
		}

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::STR and $type !== self::INT and $type !== self::DEC ) {
			$this->error("Parameter \$name must be a string.", Error::TYPE_STRING );
		}

		return new StringValidator( $this->name, $value );
	}

	/**
	 * @return DateValidator
	 * @todo string $from = null
	 */
	function date() : DateValidator
	{
		$value = $this->value;
		$type = self::detect( $value );

		if( $type === self::DEC and $value === floor( $value ) and $value >= PHP_INT_MIN and $value <= PHP_INT_MAX ) {
			$type = self::INT;
			$value = (int) $value;
		}

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::DATE and $type !== self::STR and $type !== self::INT ) {
			$this->error("Parameter \$name must be a date.", Error::TYPE_DATE );
		}

		try {
			$value = DateTime::from( $value );
		} catch( Throwable $error ) {
			$this->error("Parameter \$name must be a date.", Error::TYPE_DATE );
		}

		return new DateValidator( $this->name, $value );
	}

	/**
	 * @return MixedIterator
	 */
	function array() : MixedIterator
	{
		$type = self::detect( $this->value );

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
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
	function index() : MixedIterator
	{
		$type = self::detect( $this->value );

		if( $type === self::NULL ) {
			$this->error("Parameter \$name must be provided.", Error::TYPE_NULL );
		} elseif( $type !== self::ARR ) {
			$this->error("Parameter \$name must be an array.", Error::TYPE_ARRAY );
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
	static protected function detect( mixed $value ) : int
	{
		switch( gettype( $value )) {
			case 'NULL':
				return self::NULL;
			case 'boolean':
				return self::BOOL;
			case 'integer':
				return self::INT;
			case 'double':
				return is_finite( $value ) ? self::DEC : self::POO;
			case 'string':
				return self::STR;
			case 'array':
				return self::ARR;
			case 'resource':
			case 'resource (closed)':
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
