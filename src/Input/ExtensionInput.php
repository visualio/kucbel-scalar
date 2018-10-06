<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\CompilerExtension;

class ExtensionInput extends StrictInput
{
	/**
	 * @var ExtensionInput[] | null
	 */
	private static $inputs;

	/**
	 * @var CompilerExtension
	 */
	private $extension;

	/**
	 * @var ExistValidator
	 */
	private $validator;

	/**
	 * @var string | null
	 */
	private $section;

	/**
	 * ExtensionInput constructor.
	 *
	 * @param CompilerExtension $extension
	 * @param string $section
	 */
	function __construct( CompilerExtension $extension, string $section = null )
	{
		$hash = spl_object_hash( $extension );
		$that = self::$inputs[ $hash ] ?? null;

		if( $that ) {
			$validator = $that->validator;
		} else {
			$validator = new ExistValidator;
		}

		$this->extension = $extension;
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
		return $this->search( $this->extension->getConfig(), $this->validator->add("{$this->section}$name"), $null );
	}

	/**
	 * @param string $section
	 * @return ExtensionInput
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
	function validate()
	{
		$this->validator->match( $this->extension->getConfig(), $this->extension->prefix(''));

		$hash = spl_object_hash( $this->extension );

		unset( self::$inputs[ $hash ] );
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->extension->prefix("{$this->section}$name");
	}

	/**
	 * @throws ValidatorException
	 */
	static function compile()
	{
		foreach( self::$inputs ?? [] as $input ) {
			$input->validate();
		}
	}
}