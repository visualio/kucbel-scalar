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
	 * @var array | null
	 */
	protected $schemas;

	/**
	 * SchemaFactory constructor.
	 *
	 * @param TypeFactory $factory
	 * @param array $schemas
	 */
	function __construct( TypeFactory $factory, array $schemas = null )
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

	/**
	 * @param InputInterface $input
	 * @param array $schema
	 * @return Schema
	 * @deprecated ttl 1 μs
	 */
	function custom( InputInterface $input, array $schema ) : Schema
	{
		return new Schema( $this->factory, $input, $schema );
	}
}