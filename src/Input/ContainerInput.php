<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\ContainerBuilder;

class ContainerInput extends StrictInput
{
	use SearchMethod, SectionMethod, ValidateMethod;

	/**
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * ContainerInput constructor.
	 *
	 * @param ContainerBuilder $container
	 * @param string | null $section
	 */
	function __construct( ContainerBuilder $container, string $section = null )
	{
		if( !in_array('appDir', self::$ignore, true )) {
			array_push( self::$ignore, 'appDir', 'wwwDir', 'tempDir', 'vendorDir', 'debugMode', 'productionMode', 'consoleMode');
		}

		$this->container = $container;
		$this->validator = self::validate( $container );
		$this->section = self::suffix( $section );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, mixed $null = null ) : mixed
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
