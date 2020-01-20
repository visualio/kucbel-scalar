<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\InvalidStateException;
use Nette\SmartObject;

class InputPool implements InputInterface
{
	use SmartObject;

	const
		QUERY	= 0b1,
		CHECK	= 0b10;

	/**
	 * @var InputInterface[]
	 */
	protected $inputs;

	/**
	 * @var int
	 */
	protected $index;

	/**
	 * @var bool
	 */
	protected $check = false;

	/**
	 * InputPool constructor.
	 *
	 * @param InputInterface $input
	 * @param InputInterface ...$inputs
	 */
	function __construct( InputInterface $input, InputInterface ...$inputs )
	{
		$this->inputs[] = $input;

		foreach( $inputs as $input ) {
			$this->inputs[] = $input;
		}

		$this->index = count( $inputs );
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
			} elseif( $this->check and $input->get( $name ) !== null ) {
				return $input->create( $name );
			} elseif( !$this->check and $input->has( $name )) {
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
			if( $this->check and ( $value = $input->get( $name )) !== null ) {
				return $value;
			} elseif( !$this->check and $input->has( $name )) {
				return $input->get( $name );
			}
		}

		return null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		foreach( $this->inputs as $input ) {
			if( $this->check and $input->get( $name ) !== null ) {
				return true;
			} elseif( !$this->check and $input->has( $name )) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param int $mode
	 * @return $this
	 */
	function mode( int $mode )
	{
		if( $mode & self::CHECK ) {
			$this->check = true;
		} else {
			$this->check = false;
		}

		return $this;
	}
}
