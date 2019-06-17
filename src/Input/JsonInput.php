<?php

namespace Kucbel\Scalar\Input;

use Nette\Utils\Json;
use Nette\Utils\JsonException;
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
				if( is_object( $values ) and property_exists( $values, $part )) {
					$values = $values->$part;
				} elseif( is_array( $values ) and array_key_exists( $part, $values )) {
					$values = $values[ $part ];
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

	/**
	 * @param string $json
	 * @return JsonInput
	 * @throws JsonException
	 */
	static function decode( string $json ) : JsonInput
	{
		if( !$json or $json[0] !== '{' or $json[-1] !== '}') {
			throw new JsonException('Syntax error');
		}

		return new self( Json::decode( $json ));
	}
}
