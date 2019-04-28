<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\ContainerBuilder;

class ContainerInput extends StrictInput
{
	/**
	 * @var ExistValidator[] | null
	 */
	static private $exists;

	/**
	 * @var ContainerBuilder
	 */
	private $container;

	/**
	 * @var ExistValidator
	 */
	private $validator;

	/**
	 * @var string | null
	 */
	private $section;

	/**
	 * ContainerInput constructor.
	 *
	 * @param ContainerBuilder $container
	 * @param string $section
	 */
	function __construct( ContainerBuilder $container, string $section = null )
	{
		$hash = spl_object_hash( $container );

		$this->container = $container;
		$this->validator = self::$exists[ $hash ] ?? self::$exists[ $hash ] = new ExistValidator('appDir', 'wwwDir', 'debugMode', 'productionMode', 'consoleMode');
		$this->section = self::suffix( $section );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		$this->validator->add( $name = $this->section . $name );

		return $this->search( $this->container->parameters, $name, $null );
	}

	/**
	 * @param string $section
	 * @return ContainerInput
	 */
	function section( ?string $section ) : self
	{
		$that = clone $this;
		$that->section = self::suffix( $section );

		return $that;
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