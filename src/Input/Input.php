<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

abstract class Input implements InputInterface
{
	use SmartObject;

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		return new MixedValidator( $this->alias( $name ), $this->get( $name ));
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->get( $name ) !== null;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $name;
	}

	/**
	 * @param array $values
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	protected function search( array $values, string $name, $null = null )
	{
		if( strpos( $name, '.')) {
			foreach( explode('.', $name ) as $part ) {
				if( is_array( $values ) and array_key_exists( $part, $values )) {
					$values = $values[ $part ];
				} else {
					$values = $null;
					break;
				}
			}
		} elseif( array_key_exists( $name, $values )) {
			$values = $values[ $name ];
		} else {
			$values = $null;
		}

		return $values;
	}

	/**
	 * @param string | null $name
	 * @return string | null
	 */
	static function suffix( ?string $name ) : ?string
	{
		$last = $name[-1] ?? null;

		if( $last === null or $name === '.') {
			return null;
		} elseif( $last === '.') {
			return $name;
		} else {
			return "{$name}.";
		}
	}
}