<?php

namespace Kucbel\Scalar;

use DateTimeInterface;
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
		TYPE_CLASS		= 6, /** @deprecated */
		TYPE_DATE		= 7,
		TYPE_ARRAY		= 8,
		TYPE_VOID		= 9,

		MIX_EQUAL		= 21,
		MIX_VALUE		= 22,
		MIX_MATCH		= 23,

		NUM_DIGIT		= 41,
		NUM_POINT		= 42,
		NUM_MODULO		= 43,

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
	 * @param string $name
	 * @param string $text
	 * @param array | null $values
	 * @return string
	 */
	static function compose( string $name, string $text, array $values = null ) : string
	{
		$values['name'] = $name;
		$values = self::flatten( $values );

		return Strings::replace( $text, '~\$([a-z]+)~', function( $match ) use( $values ) {
			return $values[ $match[1] ] ?? '???';
		});
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	static function export( mixed $value ) : string
	{
		if( $value instanceof DateTimeInterface ) {
			return $value->format('Y-m-d H:i:s');
		} elseif( is_bool( $value ) or is_integer( $value ) or is_float( $value )) {
			return var_export( $value, true );
		} elseif( is_string( $value )) {
			$value = Strings::truncate( $value, 100 );

			return "\"{$value}\"";
		} elseif( is_array( $value )) {
			return self::implode( $value, 100 );
		} else {
			return strtolower( gettype( $value ));
		}
	}

	/**
	 * @param array $values
	 * @return string[]
	 */
	static function flatten( array $values ) : array
	{
		foreach( $values as $name => $value ) {
			$values[ $name ] = self::export( $value );
		}

		return $values;
	}

	/**
	 * @param array $values
	 * @param int $length
	 * @return string
	 */
	static function implode( array $values, int $length ) : string
	{
		$values = self::flatten( $values );
		$checks = [];

		foreach( $values as $value ) {
			$checks[] = $value;
			$length -= strlen( $value );

			if( $length <= 0 ) {
				break;
			}
		}

		if( count( $values ) !== count( $checks )) {
			$checks[] = '...';
		}

		return implode(', ', $values );
	}
}
