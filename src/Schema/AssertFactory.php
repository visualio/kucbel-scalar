<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputFactory;
use Kucbel\Scalar\Input\InputInterface;
use Nette\SmartObject;

class AssertFactory
{
	use SmartObject;

	/**
	 * @var InputFactory
	 */
	protected $input;

	/**
	 * @var TypeFactory
	 */
	protected $type;

	/**
	 * AssertFactory constructor.
	 *
	 * @param InputFactory $input
	 * @param TypeFactory $type
	 */
	function __construct( InputFactory $input, TypeFactory $type )
	{
		$this->input = $input;
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
	 * @param mixed $value
	 * @param string $type
	 * @param string $name
	 * @return mixed
	 */
	function value( $value, string $type, string $name = '???')
	{
		$mixed = $this->input->value( $value, $name );

		return $this->type->get( $type )->fetch( $mixed );
	}
}
