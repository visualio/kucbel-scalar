<?php

namespace Kucbel\Scalar;

use Kucbel\Scalar\Validator\ValidatorException;
use Nette\SmartObject;

abstract class Property
{
	use SmartObject;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @param int $code
	 * @param string $text
	 * @param array | null $values
	 */
	protected function error( string $text, int $code, array $values = null ) : void
	{
		$text = Error::compose( $this->name, $text, $values );

		throw new ValidatorException( $this->name, $text, $code );
	}
}