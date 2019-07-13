<?php

namespace Kucbel\Scalar\Input;

trait InputSection
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
		$that->section = self::suffix( $section );

		return $that;
	}
}
