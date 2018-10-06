<?php

namespace Kucbel\Scalar\Validator;

use JsonSerializable;
use Kucbel\Scalar\Error;
use Kucbel\Scalar\Exception;

class ValidatorException extends Exception implements JsonSerializable
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $values;

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

		parent::__construct( Error::createMessage( $name, $code, $values ), $code );
	}

	/**
	 * @return string
	 */
	function getParameter() : string
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