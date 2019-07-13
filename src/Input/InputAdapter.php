<?php

namespace Kucbel\Scalar\Input;

use Nette\InvalidArgumentException;
use Nette\SmartObject;

abstract class InputAdapter implements InputInterface
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
	 * @var int
	 */
	protected $count;

	/**
	 * @var int
	 */
	protected $mode = self::QUERY;

	/**
	 * InputAdapter constructor.
	 *
	 * @param InputInterface ...$inputs
	 */
	function __construct( InputInterface ...$inputs )
	{
		if( !$inputs ) {
			throw new InvalidArgumentException('No inputs provided.');
		}

		$this->inputs = $inputs;
		$this->count = count( $inputs );
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		foreach( $this->inputs as $input ) {
			if( $this->mode & self::CHECK ) {
				if( $input->get( $name ) !== null ) {
					return true;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $input->has( $name )) {
					return true;
				}
			} else {
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
		$this->mode = $mode;

		return $this;
	}
}
