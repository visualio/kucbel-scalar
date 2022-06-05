<?php

namespace Kucbel\Scalar\Input;

trait SectionMethod
{
	/**
	 * @var string | null
	 */
	protected $section;

	/**
	 * @param string | null $section
	 * @return static
	 */
	function withSection( ?string $section ) : static
	{
		$that = clone $this;
		$that->section = Input::suffix( $section );

		return $that;
	}
}
