<?php

namespace Kucbel\Scalar\Input;

use Symfony\Component\Console\Input\InputInterface;

class ConsoleInput extends Input implements DetectInterface
{
	/**
	 * @var InputInterface
	 */
	protected $input;

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
		if( $this->input->hasArgument( $name )) {
			return $this->input->getArgument( $name );
		} elseif( $this->input->hasOption( $name )) {
			return $this->input->getOption( $name );
		} else {
			return null;
		}
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool
	{
		return $source instanceof InputInterface;
	}
}
