<?php

namespace Kucbel\Scalar\Input;

use Nette\Application\UI\Component;

class ComponentInput extends StrictInput implements DetectInterface
{
	use SearchMethod, SectionMethod;

	/**
	 * @var Component
	 */
	protected $component;

	/**
	 * ComponentInput constructor.
	 *
	 * @param Component $component
	 * @param string | null $section
	 */
	function __construct( Component $component, string $section = null )
	{
		$this->component = $component;
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
		$value = $this->component->getParameters();

		return $this->search( $name, $value ) ? $value : $null;
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( mixed $source ) : bool
	{
		return $source instanceof Component;
	}
}
