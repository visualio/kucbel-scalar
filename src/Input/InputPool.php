<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;

class InputPool extends InputAdapter
{
	/**
	 * @var int
	 */
	protected $index;

	/**
	 * InputPool constructor.
	 *
	 * @param InputInterface ...$inputs
	 */
	function __construct( InputInterface ...$inputs )
	{
		parent::__construct( ...$inputs );

		$this->index = count( $inputs ) - 1;
	}

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		foreach( $this->inputs as $index => $input ) {
			$mixed = $input->create( $name );

			if( $this->index === $index ) {
				return $mixed;
			} elseif( $this->mode & self::CHECK ) {
				if( $mixed->fetch() !== null ) {
					return $mixed;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $mixed->fetch() !== null or $input->has( $name )) {
					return $mixed;
				}
			} else {
				return $mixed;
			}
		}

		return new MixedValidator( $name, null );
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		foreach( $this->inputs as $index => $input ) {
			$value = $input->get( $name );

			if( $this->index === $index ) {
				return $value;
			} elseif( $this->mode & self::CHECK ) {
				if( $value !== null ) {
					return $value;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $value !== null or $input->has( $name )) {
					return $value;
				}
			} else {
				return $value;
			}
		}

		return null;
	}
}