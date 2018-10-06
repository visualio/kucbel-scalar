<?php

namespace Kucbel\Scalar\Schema;

use Nette\SmartObject;
use Throwable;

class TypeFactory
{
	use SmartObject;

	/**
	 * @var TypeInterface[]
	 */
	protected $types;

	/**
	 * @var string[]
	 */
	protected $tests;

	/**
	 * TypeFactory constructor.
	 *
	 * @param array $tests
	 */
	function __construct( array $tests )
	{
		$this->tests = $tests;
	}

	/**
	 * @param string $name
	 * @param TypeInterface $type
	 * @return $this
	 */
	function add( string $name, TypeInterface $type )
	{
		$this->types[ $name ] = $type;

		return $this;
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
	 * @return TypeInterface
	 */
	protected function create( string $name ) : TypeInterface
	{
		$code = $this->tests[ $name ] ?? null;

		if( $code === null ) {
			throw new SchemaException("Type '$name' doesn't exist.");
		}

		try {
			$test = eval( $code );
		} catch( Throwable $ex ) {
			throw new SchemaException("Type '$name' didn't compile.", null, $ex );
		}

		return new Type( $test );
	}
}