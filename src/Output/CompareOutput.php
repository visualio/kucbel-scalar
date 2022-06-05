<?php

namespace Kucbel\Scalar\Output;

use Kucbel\Scalar\Input\InputInterface;
use Nette\SmartObject;

class CompareOutput implements OutputInterface
{
	use SmartObject;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * CompareOutput constructor.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	function __construct( InputInterface $input, OutputInterface $output )
	{
		$this->input = $input;
		$this->output = $output;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, mixed $value )
	{
		$exist = $this->input->get( $name );

		if( !$this->equal( $exist, $value )) {
			$this->output->set( $name, $value );
		}
	}

	/**
	 * @param mixed $old
	 * @param mixed $new
	 * @return bool
	 */
	protected function equal( mixed $old, mixed $new ) : bool
	{
		if( is_object( $old ) and is_object( $new )) {
			return $old == $new;
		} else {
			return $old === $new;
		}
	}
}
