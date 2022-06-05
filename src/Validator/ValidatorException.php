<?php

namespace Kucbel\Scalar\Validator;

use JsonSerializable;
use Kucbel\Scalar\Exception;
use Kucbel\Scalar\Schema\AssertException;
use Throwable;

class ValidatorException extends Exception implements JsonSerializable
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * ValidatorException constructor.
	 *
	 * @param string $name
	 * @param string $text
	 * @param int | null $code
	 * @param Throwable | null $error
	 */
	function __construct( string $name, string $text, int $code = null, Throwable $error = null )
	{
		parent::__construct( $text, $code ?? 0, $error );

		$this->name = $name;
	}

	/**
	 * @return string
	 */
	function getName() : string
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	function jsonSerialize() : array
	{
		return [
			'message'	=> $this->message,
			'code'		=> $this->code,
			'name'		=> $this->name,
		];
	}

	/**
	 * @param string $type
	 * @return AssertException
	 */
	function withType( string $type ) : AssertException
	{
		return new AssertException( $type, $this );
	}
}
