<?php

namespace Kucbel\Scalar\Input;

use Nette\Application\UI\Component;

class ComponentInput extends Input implements DetectInterface
{
	/**
	 * @var Component
	 */
	protected $component;

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

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool
	{
		return $source instanceof Component;
	}
}
