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
class AssertException extends Exception implements JsonSerializable
{
	/**
	 * @var ValidatorException
	 */
	private $error;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * AssertException constructor.
	 *
	 * @param ValidatorException $error
	 * @param string $type
	 */
	function __construct( ValidatorException $error, string $type )
	{
		parent::__construct( $error->getMessage(), $error->getCode(), $error );

		$this->error = $error;
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	function getName() : string
	{
		return $this->error->getName();
	}

	/**
	 * @return string
	 */
	function getType() : string
	{
		return $this->type;
	}

	/**
	 * @return array | null
	 */
	function getValues() : ?array
	{
		return $this->error->getValues();
	}

	/**
	 * @return array
	 */
	function jsonSerialize()
	{
		return $this->error->jsonSerialize();
	}
}
