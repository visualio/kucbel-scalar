<?php

namespace Kucbel\Scalar\Input;

use Symfony\Component\Console\Input\InputInterface;

class ConsoleInput extends Input
{
	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * ConsoleInput constructor.
	 *
	 * @param InputInterface $input
	 */
	function __construct( InputInterface $input )
	{
		$this->input = $input;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->input->hasArgument( $name ) ? $this->input->getArgument( $name ) : null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->input->hasArgument( $name );
	}
}