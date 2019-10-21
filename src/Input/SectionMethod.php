<?php

namespace Kucbel\Scalar\Input;

trait SectionMethod
{
	/**
	 * @var string | null
	 */
	protected $section;

	/**
	 * @param string $section
	 * @return static
	 */
	function section( ?string $section ) : self
	{
		$that = clone $this;
		$that->section = Input::suffix( $section );

		return $that;
	}
}
