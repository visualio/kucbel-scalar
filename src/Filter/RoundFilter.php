<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class RoundFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @var int
	 */
	protected $digit;

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
			$value = round( $value, $this->digit - floor( log10( abs( $value ))));
		}

		return $value;
	}
}
