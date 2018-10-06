<?php

namespace Kucbel\Scalar\Input;

use stdClass;

class JsonInput extends StrictInput
{
	/**
	 * @var stdClass
	 */
	private $json;

	/**
	 * JsonInput constructor.
	 *
	 * @param stdClass $json
	 */
	function __construct( stdClass $json )
	{
		$this->json = $json;
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		$values = $this->json;

		if( strpos( $name, '.')) {
			foreach( explode('.', $name ) as $part ) {
				if( $values instanceof stdClass and property_exists( $values, $part )) {
					$values = $values->$part;
				} else {
					$values = $null;
					break;
				}
			}
		} elseif( property_exists( $values, $name )) {
			$values = $values->$name;
		} else {
			$values = $null;
		}

		return $values;
	}
}