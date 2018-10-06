<?php

namespace Kucbel\Scalar\Input;

use Nette\Application\UI\Component;

class ComponentInput extends Input
{
	/**
	 * @var Component
	 */
	private $component;

	/**
	 * ComponentInput constructor.
	 *
	 * @param Component $component
	 */
	function __construct( Component $component )
	{
		$this->component = $component;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->component->getParameter( $name );
	}
}