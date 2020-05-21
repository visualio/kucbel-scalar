<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\ValidatorException;
use Nette\DI\CompilerExtension;

class ExtensionInput extends StrictInput
{
	use SearchMethod, SectionMethod, ValidateMethod;

	/**
	 * @var CompilerExtension
	 */
	protected $extension;

	/**
	 * ExtensionInput constructor.
	 *
	 * @param CompilerExtension $extension
	 * @param string $section
	 */
	function __construct( CompilerExtension $extension, string $section = null )
	{
		$this->extension = $extension;
		$this->validator = self::validate( $extension );
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
		$value = $this->extension->getConfig();

		$this->validator->add( $name );

		return $this->search( $name, $value ) ? $value : $null;
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
