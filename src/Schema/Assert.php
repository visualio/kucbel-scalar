<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\SmartObject;

class Assert
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $type;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * Assert constructor.
	 *
	 * @param TypeFactory $type
	 * @param InputInterface $input
	 */
	function __construct( TypeFactory $type, InputInterface $input )
	{
		$this->type = $type;
		$this->input = $input;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return bool
	 */
	function has( string $name, string $type = null ) : bool
	{
		if( $type === null ) {
			return $this->input->has( $name );
		}

		try {
			$this->type->get( $type )->fetch( $this->input->create( $name ));

			return true;
		} catch( ValidatorException $ex ) {
			return false;
		}
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return mixed
	 */
	function get( string $name, string $type = null )
	{
		if( $type === null ) {
			return $this->input->get( $name );
		}

		try {
			return $this->type->get( $type )->fetch( $this->input->create( $name ));
		} catch( ValidatorException $ex ) {
			throw new AssertException("Parameter '{$ex->getName()}' isn't valid '{$type}' type.", null, $ex );
		}
	}
}
