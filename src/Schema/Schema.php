<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Input\VoidInput;
use Kucbel\Scalar\Output\ArrayOutput;
use Kucbel\Scalar\Output\CompareOutput;
use Kucbel\Scalar\Output\OutputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Nette\SmartObject;

class Schema
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
	 * @var array
	 */
	protected $schema;

	/**
	 * Schema constructor.
	 *
	 * @param TypeFactory $type
	 * @param InputInterface $input
	 * @param array $schema
	 */
	function __construct( TypeFactory $type, InputInterface $input, array $schema )
	{
		$this->type = $type;
		$this->input = $input;
		$this->schema = $schema;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function value( string $name )
	{
		$type = $this->schema[ $name ] ?? null;

		if( $type === null ) {
			throw new SchemaException("Parameter '$name' doesn't exist.");
		}

		return $this->type->get( $type )->fetch( $this->input->create( $name ));
	}

	/**
	 * @param OutputInterface $output
	 */
	function write( OutputInterface $output )
	{
		foreach( $this->schema as $name => $type ) {
			$output->set( $name, $this->type->get( $type )->fetch( $this->input->create( $name )));
		}
	}

	/**
	 * @param InputInterface $input
	 * @return array | null
	 */
	function diff( InputInterface $input ) : ?iterable
	{
		$this->write( new CompareOutput( $input, $output = new ArrayOutput ));

		return $output->fetch();
	}

	/**
	 * @param bool $all
	 * @return array | null
	 */
	function fetch( bool $all = false ) : ?iterable
	{
		if( $all ) {
			$this->write( $output = new ArrayOutput );
		} else {
			$this->write( new CompareOutput( new VoidInput, $output = new ArrayOutput ));
		}

		return $output->fetch();
	}
}
