<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Kucbel\Scalar\Validator\MixedValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\SmartObject;

class AssertFactory
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $type;

	/**
	 * AssertFactory constructor.
	 *
	 * @param TypeFactory $type
	 */
	function __construct( TypeFactory $type )
	{
		$this->type = $type;
	}

	/**
	 * @param InputInterface $input
	 * @return Assert
	 */
	function create( InputInterface $input ) : Assert
	{
		return new Assert( $this->type, $input );
	}

	/**
	 * @param MixedValidator $value
	 * @param string $type
	 * @return mixed
	 */
	function value( MixedValidator $value, string $type )
	{
		try {
			return $this->type->get( $type )->fetch( $value );
		} catch( ValidatorException $ex ) {
			throw new AssertException("Parameter '{$ex->getName()}' isn't valid '{$type}' type.", null, $ex );
		}
	}
}
