<?php

namespace Kucbel\Scalar\Validator;

use ArrayIterator;
use Kucbel\Scalar\Error;
use Kucbel\Scalar\Input\Input;
use Nette\SmartObject;

class ExistValidator
{
	use SmartObject;

	/**
	 * @var array | null
	 */
	private $schema;

	/**
	 * @param string $name
	 * @return string
	 */
	function add( string $name ) : string
	{
		$schema = &$this->schema;

		foreach( explode('.', $name ) as $part ) {
			$schema = &$schema[ $part ];
		}

		if( $schema === null ) {
			$schema = null;
		}

		return $name;
	}

	/**
	 * @param array $config
	 * @param string $prefix
	 * @throws ValidatorException
	 */
	function match( array $config, string $prefix = null )
	{
		$prefix = Input::suffix( $prefix );

		$queue = new ArrayIterator;
		$queue[] = [ $prefix, $config, $this->schema ?? [] ];

		foreach( $queue as $next ) {
			[ $prefix, $config, $schema ] = $next;

			foreach( $config as $name => $value ) {
				if( !array_key_exists( $name, $schema )) {
					throw new ValidatorException("{$prefix}{$name}", Error::TYPE_VOID );
				}
			}

			foreach( $schema as $name => $value ) {
				if( array_key_exists( $name, $config ) and is_array( $config[ $name ] ) and is_array( $value )) {
					$suffix = Input::suffix("{$prefix}{$name}");

					$queue[] = [ $suffix, $config[ $name ], $value ];
				}
			}
		}
	}
}