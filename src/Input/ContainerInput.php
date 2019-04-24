<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\ContainerBuilder;

class ContainerInput extends StrictInput
{
	/**
	 * @var ContainerInput[] | null
	 */
	static private $inputs;

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

		if( $that = self::$inputs[ $hash ] ?? null ) {
			$validator = $that->validator;
		} else {
			$validator = new ExistValidator('appDir', 'wwwDir', 'debugMode', 'productionMode', 'consoleMode');
		}

		$this->container = $container;
		$this->validator = $validator;
		$this->section = self::suffix( $section );

		self::$inputs[ $hash ] = $this;
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
	function match()
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

	/**
	 * @throws ValidatorException
	 */
	static function close()
	{
		foreach( self::$inputs ?? [] as $input ) {
			$input->match();
		}
	}
}