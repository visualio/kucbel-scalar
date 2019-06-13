<?php

namespace Kucbel\Scalar\Filter;

class RoundFilter extends Filter
{
	/**
	 * @var int
	 */
	private $digit;

	/**
	 * RoundFilter constructor.
	 *
	 * @param int $digit
	 */
	function __construct( int $digit )
	{
		$this->digit = $digit;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( $value )
	{
		if( $value and is_float( $value )) {
			$value = round( $value, $this->digit - floor( log10( $value )));
		}

		return $value;
	}
}