<?php

namespace Kucbel\Scalar;

use DateTimeInterface;
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
		TYPE_DATE		= 6,
		TYPE_ARRAY		= 7,
		TYPE_VOID		= 8,

		SCA_EQUAL		= 21,
		SCA_OPTION		= 22,
		SCA_REGEX		= 23,

		NUM_VAL_GTE		= 41,
		NUM_VAL_GT		= 42,
		NUM_VAL_LTE		= 43,
		NUM_VAL_LT		= 44,
		NUM_VAL_RNG		= 45,

		NUM_DIGIT		= 61,
		NUM_POINT		= 62,

		STR_LEN_GTE		= 81,
		STR_LEN_GT		= 82,
		STR_LEN_LTE		= 83,
		STR_LEN_LT		= 84,
		STR_LEN_RNG		= 85,
		STR_LEN_EQ		= 86,

		STR_EMAIL		= 101,
		STR_URL			= 102,
		STR_DIR			= 103,
		STR_FILE		= 104,
		STR_CLASS		= 105,
		STR_INTER		= 106,

		DATE_VAL_GTE	= 121,
		DATE_VAL_GT		= 122,
		DATE_VAL_LTE	= 123,
		DATE_VAL_LT		= 124,
		DATE_VAL_RNG	= 125,

		ARR_LEN_GTE		= 141,
		ARR_LEN_GT		= 142,
		ARR_LEN_LTE		= 143,
		ARR_LEN_LT		= 144,
		ARR_LEN_RNG		= 145,
		ARR_LEN_EQ		= 146,

		ARR_UNIQUE		= 161;

	/**
	 * @param string $name
	 * @param int $code
	 * @param array $values
	 * @return string
	 */
	static function createMessage( string $name, int $code, array $values = null ) : string
	{
		$values = Error::getHints( $values ?? [] );
		$values['name'] = $name;

		$message = Error::getMessage( $code );
		$message = Strings::replace( $message, '~\$([a-z]+)~', function( $match ) use( $values ) {
			return $values[ $match[1] ] ?? '???';
		});

		return $message;
	}

	/**
	 * @param int $code
	 * @return string
	 */
	static function getMessage( int $code ) : string
	{
		switch( $code ) {
			case Error::TYPE_NULL:		return 'Parameter $name must be provided.';
			case Error::TYPE_BOOL:		return 'Parameter $name must be a boolean.';
			case Error::TYPE_FLOAT:		return 'Parameter $name must be a float.';
			case Error::TYPE_INTEGER:	return 'Parameter $name must be an integer.';
			case Error::TYPE_STRING:	return 'Parameter $name must be a string.';
			case Error::TYPE_DATE:		return 'Parameter $name must be a date.';
			case Error::TYPE_ARRAY:		return 'Parameter $name must be an array.';
			case Error::TYPE_VOID:		return 'Parameter $name does not exist.';

			case Error::SCA_EQUAL:		return 'Parameter $name must be equal to $val.';
			case Error::SCA_OPTION:		return 'Parameter $name must be one the following $opts.';
			case Error::SCA_REGEX:		return 'Parameter $name must match $regex pattern.';

			case Error::NUM_VAL_GTE:
			case Error::DATE_VAL_GTE:	return 'Parameter $name must be equal to or greater than $min.';
			case Error::NUM_VAL_GT:
			case Error::DATE_VAL_GT:	return 'Parameter $name must be greater than $min.';
			case Error::NUM_VAL_LTE:
			case Error::DATE_VAL_LTE:	return 'Parameter $name must be equal to or less than $max.';
			case Error::NUM_VAL_LT:
			case Error::DATE_VAL_LT:	return 'Parameter $name must be less than $max.';
			case Error::NUM_VAL_RNG:
			case Error::DATE_VAL_RNG:	return 'Parameter $name must be between $min and $max.';

			case Error::NUM_DIGIT:		return 'Parameter $name must have $dig or fewer digits.';
			case Error::NUM_POINT:		return 'Parameter $name must have $dec or fewer decimal digits.';

			case Error::STR_LEN_GTE:	return 'Parameter $name must be at least $min characters long.';
			case Error::STR_LEN_GT:		return 'Parameter $name must be more then $min characters long.';
			case Error::STR_LEN_LTE:	return 'Parameter $name must be at most $max characters long.';
			case Error::STR_LEN_LT:		return 'Parameter $name must be fewer than $max characters long.';
			case Error::STR_LEN_RNG:	return 'Parameter $name must be between $min and $max characters long.';
			case Error::STR_LEN_EQ:		return 'Parameter $name must be exactly $len characters long.';

			case Error::STR_EMAIL:		return 'Parameter $name must be an email.';
			case Error::STR_URL:		return 'Parameter $name must be an url.';
			case Error::STR_DIR:		return 'Parameter $name must point to a directory.';
			case Error::STR_FILE:		return 'Parameter $name must point to a file.';
			case Error::STR_CLASS:		return 'Parameter $name must extend $class class.';
			case Error::STR_INTER:		return 'Parameter $name must implement $inter interface.';

			case Error::ARR_LEN_GTE:	return 'Parameter $name must contain at least $min values.';
			case Error::ARR_LEN_GT:		return 'Parameter $name must contain more then $min values.';
			case Error::ARR_LEN_LTE:	return 'Parameter $name must contain at most $max values.';
			case Error::ARR_LEN_LT:		return 'Parameter $name must contain fewer that $max values.';
			case Error::ARR_LEN_RNG:	return 'Parameter $name must contain between $min and $max values.';
			case Error::ARR_LEN_EQ:		return 'Parameter $name must contain exactly $len values.';

			case Error::ARR_UNIQUE:		return 'Parameter $name must contain unique values.';

			default:
				throw new InvalidArgumentException("Error code $code doesn't exist.");
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

			return "\"$value\"";
		} elseif( is_array( $value )) {
			return Strings::truncate( implode(', ', self::getHints( $value )), 100 );
		} elseif( $value instanceof DateTimeInterface ) {
			$date = $value->format('Y-m-d');
			$time = $value->format('H:i:s');

			return $time === '00:00:00' ? $date : "$date $time";
		} else {
			return strtolower( gettype( $value ));
		}
	}
}