<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ExistValidator;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\CompilerExtension;

class ExtensionInput extends StrictInput
{
	/**
	 * @var ExistValidator[] | null
	 */
	static private $exists;

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

		$this->extension = $extension;
		$this->validator = self::$exists[ $hash ] ?? self::$exists[ $hash ] = new ExistValidator;
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

		return $this->search( $this->extension->getConfig(), $name, $null );
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
	function match() : void
	{
		$this->validator->match( $this->extension->getConfig(), $this->extension->prefix(''));
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->extension->prefix( $this->section . $name );
	}
}