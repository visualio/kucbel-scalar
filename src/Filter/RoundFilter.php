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
	 * @var int
	 */
	protected $limit;

	/**
	 * RoundFilter constructor.
	 *
	 * @param int $digit
	 * @param int $limit
	 */
	function __construct( int $digit, int $limit )
	{
		$this->digit = $digit - 1;
		$this->limit = $limit;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function value( $value )
	{
		if( $value and is_float( $value ) and is_finite( $value )) {
			$point = $this->digit - floor( log10( abs( $value )));
			$point = min( $this->limit, $point );
			$value = round( $value, $point );
		}

		return $value;
	}
}
