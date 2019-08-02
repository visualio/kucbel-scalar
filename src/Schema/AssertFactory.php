<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
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
	 * @param TypeFactory $factory
	 */
	function __construct( TypeFactory $factory )
	{
		$this->factory = $factory;
	}

	/**
	 * @param InputInterface $input
	 * @return Assert
	 */
	function create( InputInterface $input ) : Assert
	{
		return new Assert( $this->factory, $input );
	}
}
