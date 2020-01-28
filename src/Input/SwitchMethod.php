<?php

namespace Kucbel\Scalar\Input;

trait SwitchMethod
{
	/**
	 * @var string | null
	 */
	protected $section;

	/**
	 * @param string $section
	 * @return static
	 */
	function switch( ?string $section ) : self
	{
		$that = clone $this;
		$that->section = Input::suffix( $section );

		return $that;
	}
}
