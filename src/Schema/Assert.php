<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\SmartObject;

class Assert
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	private $factory;

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * Assert constructor.
	 *
	 * @param TypeFactory $factory
	 * @param InputInterface $input
	 */
	function __construct( TypeFactory $factory, InputInterface $input )
	{
		$this->factory = $factory;
		$this->input = $input;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return mixed
	 */
	function fetch( string $name, string $type )
	{
		return $this->factory->get( $type )->fetch( $this->input->create( $name ));
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed $data
	 * @return bool
	 */
	function match( string $name, string $type, &$data = null ) : bool
	{
		try {
			$data = $this->factory->get( $type )->fetch( $this->input->create( $name ));

			return true;
		} catch( ValidatorException $ex ) {
			return false;
		}
	}
}
