<?php

namespace Kucbel\Scalar\Schema;

use JsonSerializable;
use Kucbel\Scalar\Exception;

/**
 * Class AssertException
 *
 * @method AssertException getPrevious()
 */
class SchemaException extends Exception implements JsonSerializable
{
	/**
	 * @var AssertException[]
	 */
	protected $errors;

	/**
	 * SchemaException constructor.
	 *
	 * @param AssertException $error
	 * @param AssertException ...$errors
	 */
	function __construct( AssertException $error, AssertException ...$errors )
	{
		parent::__construct( $error->getMessage(), null, $error );

		$this->errors[] = $error;

		foreach( $errors as $error ) {
			$this->errors[] = $error;
		}
	}

	/**
	 * @return AssertException[]
	 */
	function getErrors() : array
	{
		return $this->errors;
	}

	/**
	 * @return array
	 */
	function jsonSerialize() : array
	{
		$json = [];

		foreach( $this->errors as $error ) {
			$json[] = $error->jsonSerialize();
		}

		return $json;
	}
}
