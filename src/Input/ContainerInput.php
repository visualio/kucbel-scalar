<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\ContainerBuilder;

class ContainerInput extends StrictInput
{
	use InputSearch, InputSection, InputValidate;

	/**
	 * @var string[]
	 */
	protected static $ignore = ['appDir', 'wwwDir', 'debugMode', 'productionMode', 'consoleMode'];

	/**
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * ContainerInput constructor.
	 *
	 * @param ContainerBuilder $container
	 * @param string $section
	 */
	function __construct( ContainerBuilder $container, string $section = null )
	{
		$this->container = $container;
		$this->validator = self::validate( $container );
		$this->section = self::suffix( $section );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		$name = $this->section . $name;
		$value = $this->container->parameters;

		$this->validator->add( $name );

		return $this->search( $name, $value ) ? $value : $null;
	}

	/**
	 * @throws ValidatorException
	 */
	function match() : void
	{
		$this->validator->match( $this->container->parameters, 'parameters');
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return "parameters.{$this->section}{$name}";
	}
}
