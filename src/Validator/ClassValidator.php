<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

/**
 * Class ClassValidator
 *
 * @method string fetch()
 */
class ClassValidator extends ScalarValidator
{
	/**
	 * @var ReflectionClass
	 */
	protected $ref;

	/**
	 * ClassValidator constructor.
	 *
	 * @param string $name
	 * @param string $value
	 */
	function __construct( string $name, string $value )
	{
		$this->name = $name;
		$this->value = $value = ltrim( $value, '\\');

		try {
			$this->ref = new ReflectionClass( $value );
		} catch( ReflectionException $ex ) {
			throw new ValidatorException( $name, Error::TYPE_CLASS );
		}
	}

	/**
	 * @param string ...$types
	 * @return $this
	 */
	function equal( string ...$types )
	{
		if( !$types ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( !in_array( $this->value, $types, true )) {
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['list' => $types ]);
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function extend( string $type )
	{
		if( !$this->ref->isSubclassOf( $type )) {
			throw new ValidatorException( $this->name, Error::CLA_EXTEND, ['type' => $type ]);
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function implement( string $type )
	{
		if( !$this->ref->implementsInterface( $type )) {
			throw new ValidatorException( $this->name, Error::CLA_IMPLEMENT, ['type' => $type ]);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function interface()
	{
		if( !$this->ref->isInterface() ) {
			throw new ValidatorException( $this->name, Error::CLA_INTERFACE );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function abstract()
	{
		if( $this->ref->isInterface() or $this->ref->isTrait() or $this->ref->isFinal() ) {
			throw new ValidatorException( $this->name, Error::CLA_ABSTRACT );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function concrete()
	{
		if( !$this->ref->isInstantiable() ) {
			throw new ValidatorException( $this->name, Error::CLA_CONCRETE );
		}

		return $this;
	}
}
