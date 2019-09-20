<?php

namespace Kucbel\Scalar;

use DateTimeInterface;
use Kucbel\Scalar\Validator\Validator;
use Nette\InvalidArgumentException;
use Nette\StaticClass;
use Nette\Utils\Strings;

class Error
{
	use StaticClass;

	const
		TYPE_NULL		= 1,
		TYPE_BOOL		= 2,
		TYPE_FLOAT		= 3,
		TYPE_INTEGER	= 4,
		TYPE_STRING		= 5,
		TYPE_CLASS		= 6,
		TYPE_DATE		= 7,
		TYPE_ARRAY		= 8,
		TYPE_VOID		= 9,

		MIX_EQUAL		= 21,
		MIX_VALUE		= 22,
		MIX_MATCH		= 23,

		NUM_DIGIT		= 41,
		NUM_POINT		= 42,

		STR_CHAR		= 61,
		STR_LINE		= 62,
		STR_CLASS		= 63,
		STR_EMAIL		= 64,
		STR_URL			= 65,
		STR_FILE		= 66,
		STR_FOLDER		= 67,

		ARR_COUNT		= 81,
		ARR_UNIQUE		= 82,
		ARR_EXIST		= 83;

	/**
	 * @param int $code
	 * @param array $values
	 * @return string
	 */
	static function getText( int $code, array $values = null ) : string
	{
		switch( $code ) {
			case Error::TYPE_NULL:								return 'Parameter $name must be provided.';
			case Error::TYPE_BOOL:								return 'Parameter $name must be a boolean.';
			case Error::TYPE_FLOAT:								return 'Parameter $name must be a float.';
			case Error::TYPE_INTEGER:							return 'Parameter $name must be an integer.';
			case Error::TYPE_STRING:							return 'Parameter $name must be a string.';
			case Error::TYPE_CLASS:								return 'Parameter $name must be a class.';
			case Error::TYPE_DATE:								return 'Parameter $name must be a date.';
			case Error::TYPE_ARRAY:								return 'Parameter $name must be an array.';
			case Error::TYPE_VOID:								return 'Parameter $name does not exist.';

			case Error::MIX_EQUAL:
				switch( isset( $values['list'][1] )) {
					case true:									return 'Parameter $name must be one the following $list.';
					case false:									return 'Parameter $name must be equal to $list.';
				}

			case Error::MIX_VALUE:															$text = 'Parameter $name must be';
				if( $values['min'] !== null and ~ $values['opt'] & Validator::EXCL_MIN ) {	$text .= ' equal or'; }
				if( $values['min'] !== null ) {												$text .= ' greater than $min'; }
				if( $values['min'] !== null and $values['max'] !== null ) {					$text .= ' and'; }
				if( $values['max'] !== null and ~ $values['opt'] & Validator::EXCL_MAX ) {	$text .= ' equal or'; }
				if( $values['max'] !== null ) {												$text .= ' less than $max'; }

				return "{$text}.";

			case Error::MIX_MATCH:								return 'Parameter $name must match $exp pattern.';

			case Error::NUM_DIGIT:
				switch( true ) {
					case $values['min'] === $values['max']:		return 'Parameter $name must have exactly $min digits.';
					case $values['max'] === null:				return 'Parameter $name must have at least $min digits.';
					case $values['min'] === null:				return 'Parameter $name must have at most $max digits.';
					default:									return 'Parameter $name must have between $min and $max digits.';
				}

			case Error::NUM_POINT:
				switch( true ) {
					case $values['min'] === $values['max']:		return 'Parameter $name must have exactly $min decimal digits.';
					case $values['max'] === null:				return 'Parameter $name must have at least $min decimal digits.';
					case $values['min'] === null:				return 'Parameter $name must have at most $max decimal digits.';
					default:									return 'Parameter $name must have between $min and $max decimal digits.';
				}

			case Error::STR_CHAR:
				switch( true ) {
					case $values['min'] === $values['max']:		return 'Parameter $name must be exactly $min characters long.';
					case $values['max'] === null:				return 'Parameter $name must be at least $min characters long.';
					case $values['min'] === null:				return 'Parameter $name must be at most $max characters long.';
					default:									return 'Parameter $name must be between $min and $max characters long.';
				}

			case Error::STR_LINE:
				switch( true ) {
					case $values['min'] === $values['max']:		return 'Parameter $name must have exactly $min lines.';
					case $values['max'] === null:				return 'Parameter $name must have at least $min lines.';
					case $values['min'] === null:				return 'Parameter $name must have at most $max lines.';
					default:									return 'Parameter $name must have between $min and $max lines.';
				}

			case Error::STR_CLASS:
				switch( interface_exists( $values['type'] )) {
					case true:									return 'Parameter $name must be a class implementing $type interface.';
					case false:									return 'Parameter $name must be a class extending $type parent.';
				}

			case Error::STR_EMAIL:								return 'Parameter $name must be an email.';
			case Error::STR_URL:								return 'Parameter $name must be an url.';
			case Error::STR_FILE:								return 'Parameter $name must point to a file.';
			case Error::STR_FOLDER:								return 'Parameter $name must point to a folder.';

			case Error::ARR_COUNT:
				switch( true ) {
					case $values['min'] === $values['max']:		return 'Parameter $name must contain exactly $min values.';
					case $values['max'] === null:				return 'Parameter $name must contain at least $min values.';
					case $values['min'] === null:				return 'Parameter $name must contain at most $max values.';
					default:									return 'Parameter $name must contain between $min and $max values.';
				}

			case Error::ARR_UNIQUE:								return 'Parameter $name must contain unique values.';

			case Error::ARR_EXIST:
				switch( isset( $values['list'][1] )) {
					case true:									return 'Parameter $name must contain all of the following values $list.';
					case false:									return 'Parameter $name must contain the value $list.';
				}

			default:
				throw new InvalidArgumentException("Unknown code.");
		}
	}

	/**
	 * @param array $values
	 * @return array
	 */
	static function getHints( array $values ) : array
	{
		foreach( $values as $name => $value ) {
			$values[ $name ] = self::getHint( $value );
		}

		return $values;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	static function getHint( $value ) : string
	{
		if( is_bool( $value ) or is_integer( $value ) or is_float( $value )) {
			return var_export( $value, true );
		} elseif( is_string( $value )) {
			$value = Strings::truncate( $value, 100 );

			return "\"{$value}\"";
		} elseif( is_array( $value )) {
			return Strings::truncate( implode(', ', self::getHints( $value )), 100 );
		} elseif( $value instanceof DateTimeInterface ) {
			return $value->format('Y-m-d H:i:s');
		} else {
			return strtolower( gettype( $value ));
		}
	}
}
