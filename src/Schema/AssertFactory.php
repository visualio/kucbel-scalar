<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class AssertFactory
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $factory;

	/**
	 * AssertFactory constructor.
	 *
	 * @param TypeFactory $type
	 */
	function __construct( TypeFactory $type )
	{
		$this->factory = $type;
	}

	/**
	 * @param InputInterface $input
	 * @return Assert
	 */
	function create( InputInterface $input ) : Assert
	{
		return new Assert( $this->factory, $input );
	}

	/**
	 * @param MixedValidator $value
	 * @param string $type
	 * @return mixed
	 */
	function value( MixedValidator $value, string $type )
	{
		return $this->factory->get( $type )->fetch( $value );
	}
}
