<?php

namespace Kucbel\Scalar\Validator;

use JsonSerializable;
use Kucbel\Scalar\Error;
use Kucbel\Scalar\Exception;
use Nette\Utils\Strings;

class ValidatorException extends Exception implements JsonSerializable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array | null
	 */
	private $values;

	/**
	 * ValidatorException constructor.
	 *
	 * @param string $name
	 * @param int $code
	 * @param array $values
	 */
	function __construct( string $name, int $code, array $values = null )
	{
		$this->name = $name;
		$this->values = $values;

		$text = Error::getText( $code, $values );

		$values['name'] = $name;
		$values = Error::getHints( $values );

		$text = Strings::replace( $text, '~\$([a-z]+)~', function( $match ) use( $values ) {
			return $values[ $match[1] ] ?? '???';
		});

		parent::__construct( $text, $code );
	}

	/**
	 * @return string
	 */
	function getName() : string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function getValue( string $name )
	{
		return $this->values[ $name ] ?? null;
	}

	/**
	 * @return array | null
	 */
	function getValues() : ?array
	{
		return $this->values;
	}

	/**
	 * @inheritdoc
	 */
	function jsonSerialize()
	{
		return [
			'message'	=> $this->message,
			'code'		=> $this->code,
			'name'		=> $this->name,
			'values'	=> $this->values,
		];
	}
}