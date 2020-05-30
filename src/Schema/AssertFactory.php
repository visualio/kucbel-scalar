<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Kucbel\Scalar\Validator\MixedValidator;
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
		return $this->type->get( $type )->fetch( $value );
	}
}
