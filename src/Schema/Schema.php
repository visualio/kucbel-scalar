<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Input\VoidInput;
use Kucbel\Scalar\Output\CompareOutput;
use Kucbel\Scalar\Output\DirectOutput;
use Kucbel\Scalar\Output\OutputInterface;
use Nette\SmartObject;

class Schema
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
	 * @var array
	 */
	private $schema;

	/**
	 * Schema constructor.
	 *
	 * @param TypeFactory $factory
	 * @param InputInterface $input
	 * @param array $schema
	 */
	function __construct( TypeFactory $factory, InputInterface $input, array $schema )
	{
		$this->factory = $factory;
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

		return $this->factory->get( $type )->fetch( $this->input->create( $name ));
	}

	/**
	 * @param OutputInterface $output
	 */
	function write( OutputInterface $output )
	{
		foreach( $this->schema as $name => $type ) {
			$output->set( $name, $this->factory->get( $type )->fetch( $this->input->create( $name )));
		}
	}

	/**
	 * @param InputInterface $input
	 * @return array | null
	 */
	function diff( InputInterface $input ) : ?iterable
	{
		$this->write( $output = new CompareOutput( $input ));

		return $output->fetch();
	}

	/**
	 * @param bool $all
	 * @return array | null
	 */
	function fetch( bool $all = false ) : ?iterable
	{
		if( $all ) {
			$output = new DirectOutput;
		} else {
			$output = new CompareOutput( new VoidInput );
		}

		$this->write( $output );

		return $output->fetch();
	}
}