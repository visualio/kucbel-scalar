<?php

namespace Kucbel\Scalar\Schema;

use JsonSerializable;
use Kucbel\Scalar\Exception;
use Kucbel\Scalar\Validator\ValidatorException;

/**
 * Class AssertException
 *
 * @method ValidatorException getPrevious()
 */
class SchemaException extends Exception implements JsonSerializable
{
	/**
	 * @var ValidatorException[]
	 */
	private $errors;

	/**
	 * SchemaException constructor.
	 *
	 * @param ValidatorException $error
	 * @param ValidatorException ...$errors
	 */
	function __construct( ValidatorException $error, ValidatorException ...$errors )
	{
		parent::__construct( $error->getMessage(), null, $error );

		$this->errors[] = $error;

		foreach( $errors as $error ) {
			$this->errors[] = $error;
		}
	}

	/**
	 * @return ValidatorException[]
	 */
	function getErrors() : array
	{
		return $this->errors;
	}

	/**
	 * @return string[]
	 */
	function getNames() : array
	{
		$names = [];

		foreach( $this->errors as $error ) {
			$names[] = $error->getName();
		}

		return $names;
	}

	/**
	 * @return array
	 */
	function jsonSerialize()
	{
		$json = [];

		foreach( $this->errors as $error ) {
			$json[] = $error->jsonSerialize();
		}

		return ['errors' => $json ];
	}
}
