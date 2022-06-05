<?php

namespace Kucbel\Scalar\Filter;

use Nette\InvalidArgumentException;
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
	protected $point;

	/**
	 * RoundFilter constructor.
	 *
	 * @param int $digit
	 * @param int $point
	 */
	function __construct( int $digit, int $point )
	{
		if( $digit <= 0 ) {
			throw new InvalidArgumentException("Digit must be greater then zero.");
		}

		$this->digit = $digit - 1;
		$this->point = $point;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( mixed $value ) : mixed
	{
		if( $value and is_float( $value ) and is_finite( $value )) {
			$point = $this->digit - floor( log10( abs( $value )));
			$point = min( $this->point, $point );
			$value = round( $value, $point );
		}

		return $value;
	}
}
