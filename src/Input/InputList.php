<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class InputList implements InputInterface
{
	use SmartObject;

	const
		NONE	= 0,
		QUERY	= 0b1,
		CHECK	= 0b10;

	/**
	 * @var InputInterface[]
	 */
	protected $inputs;

	/**
	 * @var bool
	 */
	protected $check = false;

	/**
	 * @var bool
	 */
	protected $query = true;

	/**
	 * InputList constructor.
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
	}

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		return new MixedValidator( $name, $this->get( $name ));
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		$values = null;

		foreach( $this->inputs as $input ) {
			if( $this->check ) {
				if(( $value = $input->get( $name )) !== null ) {
					$values[] = $value;
				}
			} elseif( $this->query ) {
				if( $input->has( $name )) {
					$values[] = $input->get( $name );
				}
			} else {
				$values[] = $input->get( $name );
			}
		}

		return $values;
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
			$this->query = false;
		} elseif( $mode & self::QUERY ) {
			$this->check = false;
			$this->query = true;
		} else {
			$this->check =
			$this->query = false;
		}

		return $this;
	}
}
