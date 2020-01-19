<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\InvalidStateException;

class InputPool extends InputAdapter
{
	/**
	 * @var int
	 */
	protected $final;

	/**
	 * InputPool constructor.
	 *
	 * @param InputInterface $input
	 * @param InputInterface ...$inputs
	 */
	public function __construct( InputInterface $input, InputInterface ...$inputs )
	{
		parent::__construct( $input, ...$inputs );

		$this->final = count( $inputs );
	}

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		foreach( $this->inputs as $index => $input ) {
			if( $this->final === $index ) {
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

		throw new InvalidStateException;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		foreach( $this->inputs as $input ) {
			if( $this->mode & self::CHECK ) {
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
