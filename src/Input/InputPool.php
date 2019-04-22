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
			if( $this->index === $index ) {
				return $input->create( $name );
			} elseif( $this->mode & self::CHECK ) {
				if( $input->get( $name ) !== null ) {
					return $input->create( $name );
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $input->has( $name )) {
					return $input->create( $name );
				}
			} else {
				return $input->create( $name );
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
			if( $this->index === $index ) {
				return $input->get( $name );
			} elseif( $this->mode & self::CHECK ) {
				if(( $value = $input->get( $name )) !== null ) {
					return $value;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $input->has( $name )) {
					return $input->get( $name );
				}
			} else {
				return $input->get( $name );
			}
		}

		return null;
	}
}