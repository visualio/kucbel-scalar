<?php

namespace Kucbel\Scalar\Input;

use Nette\DI\ContainerBuilder;

class ContainerInput extends StrictInput
{
	/**
	 * @var ContainerBuilder
	 */
	private $builder;

	/**
	 * @var string | null
	 */
	private $section;

	/**
	 * ContainerInput constructor.
	 *
	 * @param ContainerBuilder $builder
	 * @param string $section
	 */
	function __construct( ContainerBuilder $builder, string $section = null )
	{
		$this->builder = $builder;
		$this->section = self::suffix( $section );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		return $this->search( $this->builder->parameters, "{$this->section}$name", $null );
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
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return "parameters.$this->section$name";
	}
}