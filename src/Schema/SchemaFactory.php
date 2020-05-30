<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Nette\SmartObject;

class SchemaFactory
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $type;

	/**
	 * @var array | null
	 */
	protected $schemas;

	/**
	 * SchemaFactory constructor.
	 *
	 * @param TypeFactory $type
	 * @param array $schemas
	 */
	function __construct( TypeFactory $type, array $schemas = null )
	{
		$this->type = $type;
		$this->schemas = $schemas;
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string ...$names
	 * @return Schema
	 */
	function create( InputInterface $input, string $name, string ...$names ) : Schema
	{
		return new Schema( $this->type, $input, $this->get( $name, ...$names ));
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string ...$names
	 * @return Schema
	 */
	function update( InputInterface $input, string $name, string ...$names ) : Schema
	{
		$filter = $this->get( $name, ...$names );
		$schema = [];

		foreach( $filter as $name => $type ) {
			if( $input->has( $name )) {
				$schema[ $name ] = $type;
			}
		}

		return new Schema( $this->type, $input, $schema );
	}

	/**
	 * @param string ...$names
	 * @return array
	 */
	protected function get( string ...$names ) : array
	{
		$verify = isset( $names[1] );

		$origins =
		$schemas = [];

		foreach( $names as $name ) {
			$schema = $this->schemas[ $name ] ?? null;

			if( $schema === null ) {
				throw new SchemaException("Schema '$name' doesn't exist.");
			}

			if( $verify ) {
				foreach( $schema as $each => $type ) {
					$same = $schemas[ $each ] ?? null;

					if( $same !== null and $same !== $type ) {
						throw new SchemaException("Schemas '{$origins[ $each ]}' and '$name' aren't compatible.");
					}

					$origins[ $each ] = $name;
					$schemas[ $each ] = $type;
				}
			} else {
				$schemas = $schema;
			}
		}

		return $schemas;
	}
}
