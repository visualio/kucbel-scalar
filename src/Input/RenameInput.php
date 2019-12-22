<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class RenameInput implements InputInterface
{
	use SmartObject;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var array
	 */
	protected $names;

	/**
	 * RenameInput constructor.
	 *
	 * @param InputInterface $input
	 * @param array $names
	 */
	function __construct( InputInterface $input, array $names )
	{
		$this->input = $input;
		$this->names = $names;
	}

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		return $this->input->create( $this->names[ $name ] ?? $name );
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->input->get( $this->names[ $name ] ?? $name );
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->input->has( $this->names[ $name ] ?? $name );
	}
}
