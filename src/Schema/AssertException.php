<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Validator\ValidatorException;

class AssertException extends ValidatorException
{
	/**
	 * @var string
	 */
	protected $type;

	/**
	 * AssertException constructor.
	 *
	 * @param string $type
	 * @param ValidatorException $error
	 */
	function __construct( string $type, ValidatorException $error )
	{
		parent::__construct( $error->getName(), $error->getMessage(), $error->getCode(), $error );

		$this->type = $type;
	}

	/**
	 * @return string
	 */
	function getType() : string
	{
		return $this->type;
	}

	/**
	 * @return array
	 */
	function jsonSerialize() : array
	{
		$json = parent::jsonSerialize();
		$json['type'] = $this->type;

		return $json;
	}
}
