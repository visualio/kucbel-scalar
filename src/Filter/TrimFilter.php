<?php

namespace Kucbel\Scalar\Filter;

use Nette\InvalidArgumentException;
use Nette\SmartObject;
use Nette\Utils\Strings;

class TrimFilter implements FilterInterface
{
	use SmartObject;

	const
		STRING	= 0b1,
		ARRAY	= 0b10,
		EMPTY	= 0b100,
		SPACE	= 0b1000,
		BREAK	= 0b10000;

	/**
	 * @var int
	 */
	protected $mode;

	/**
	 * TrimFilter constructor.
	 *
	 * @param int $mode
	 */
	function __construct( int $mode )
	{
		if( $mode & self::SPACE and ~ $mode & self::STRING ) {
			throw new InvalidArgumentException('String filter must be enabled.');
		}

		if( $mode & self::BREAK and ~ $mode & self::SPACE ) {
			throw new InvalidArgumentException('Space filter must be enabled.');
		}

		$this->mode = $mode;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( $value )
	{
		if( is_array( $value )) {
			$value = array_map([ $this, 'clear' ], $value );

			if( $this->mode & self::ARRAY ) {
				$value = array_filter( $value, [ $this, 'null']);
			}

			if( $this->mode & self::EMPTY and $value === [] ) {
				$value = null;
			}
		} elseif( is_string( $value )) {
			if( $this->mode & self::SPACE ) {
				$value = Strings::replace( $value, '~^[\pC\pZ]+|[\pC\pZ]+$|([\pC\pZ]+)~u', [ $this, $this->mode & self::BREAK ? 'space' : 'block']);
			} elseif( $this->mode & self::STRING ) {
				$value = Strings::replace( $value, '~^[\pC\pZ]+|[\pC\pZ]+$~u', '');
			}

			if( $this->mode & self::EMPTY and $value === '') {
				$value = null;
			}
		}

		return $value;
	}

	/**
	 * @param array $match
	 * @return string
	 * @internal
	 */
	function space( $match )
	{
		return isset( $match[1] ) ? ' ' : '';
	}

	/**
	 * @param array $match
	 * @return string
	 * @internal
	 */
	function block( $match )
	{
		if( isset( $match[1] )) {
			return Strings::replace( $match[1], '~[^\r\n]*(\r\n|\r|\n)[^\r\n]*|[^\r\n]+~u', [ $this, 'line']);
		} else {
			return '';
		}
	}

	/**
	 * @param array $match
	 * @return string
	 * @internal
	 */
	function line( $match )
	{
		return isset( $match[1] ) ? "\n" : ' ';
	}

	/**
	 * @param mixed $value
	 * @return bool
	 * @internal
	 */
	function null( $value )
	{
		return $value !== null;
	}
}
