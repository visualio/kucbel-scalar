<?php

namespace Kucbel\Scalar\Input;

trait SearchMethod
{
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	protected function search( string $name, mixed &$value ) : bool
	{
		if( strpos( $name, '.')) {
			foreach( explode('.', $name ) as $part ) {
				if( !$this->read( $part, $value )) {
					return false;
				}
			}
		} elseif( !$this->read( $name, $value )) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	protected function read( string $name, mixed &$value ) : bool
	{
		if( is_object( $value ) and property_exists( $value, $name )) {
			$value = $value->$name;

			return true;
		} elseif( is_array( $value ) and array_key_exists( $name, $value )) {
			$value = $value[ $name ];

			return true;
		} else {
			return false;
		}
	}
}
