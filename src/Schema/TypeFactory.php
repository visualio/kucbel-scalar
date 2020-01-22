<?php

namespace Kucbel\Scalar\Schema;

use Nette\SmartObject;
use Throwable;

class TypeFactory
{
	use SmartObject;

	/**
	 * @var TypeInterface[] | null
	 */
	protected $types;

	/**
	 * @var string[] | null
	 */
	protected $tests;

	/**
	 * TypeFactory constructor.
	 *
	 * @param array $tests
	 */
	function __construct( array $tests = null )
	{
		$this->tests = $tests;
	}

	/**
	 * @param string $name
	 * @return TypeInterface
	 */
	protected function create( string $name ) : TypeInterface
	{
		$test = $this->tests[ $name ] ?? null;

		if( $test === null ) {
			throw new SchemaException("Type '$name' doesn't exist.");
		}

		try {
			$call = eval( $test );
		} catch( Throwable $ex ) {
			throw new SchemaException("Type '$name' didn't compile.", null, $ex );
		}

		return new CallType( $call );
	}

	/**
	 * @param string $name
	 * @return TypeInterface
	 */
	function get( string $name ) : TypeInterface
	{
		return $this->types[ $name ] ?? $this->types[ $name ] = $this->create( $name );
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return isset( $this->types[ $name ] ) or isset( $this->tests[ $name ] );
	}

	/**
	 * @param string $name
	 * @param TypeInterface $type
	 * @return $this
	 */
	function set( string $name, TypeInterface $type )
	{
		if( $this->has( $name )) {
			throw new SchemaException("Type '$name' already exists.");
		}

		$this->types[ $name ] = $type;

		return $this;
	}
}
