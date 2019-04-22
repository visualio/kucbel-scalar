<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Nette\SmartObject;

class SchemaFactory
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $factory;

	/**
	 * @var array
	 */
	protected $schemas;

	/**
	 * SchemaFactory constructor.
	 *
	 * @param TypeFactory $factory
	 * @param array $schemas
	 */
	function __construct( TypeFactory $factory, array $schemas )
	{
		$this->factory = $factory;
		$this->schemas = $schemas;
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @return Schema
	 */
	function create( InputInterface $input, string $name ) : Schema
	{
		$schema = $this->schemas[ $name ] ?? null;

		if( $schema === null ) {
			throw new SchemaException("Schema '$name' doesn't exist.");
		}

		return new Schema( $this->factory, $input, $schema );
	}
}