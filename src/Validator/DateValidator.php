<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\Utils\DateTime;

/**
 * Class DateValidator
 *
 * @method DateTime fetch()
 */
class DateValidator extends Validator
{
	/**
	 * DateValidator constructor.
	 *
	 * @param string $name
	 * @param DateTime $value
	 */
	function __construct( string $name, DateTime $value )
	{
		$this->name = $name;
		$this->value = $value;
	}
	/**
	 * @param mixed $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( $limit, bool $equal = true )
	{
		$limit = DateTime::from( $limit );

		if( $equal and $this->value > $limit ) {
			$this->error( Error::DATE_VAL_LTE, ['max'=> $limit ]);
		} elseif( !$equal and $this->value >= $limit ) {
			$this->error( Error::DATE_VAL_LT, ['max'=> $limit ]);
		}

		return $this;
	}

	/**
	 * @param mixed $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( $limit, bool $equal = true )
	{
		$limit = DateTime::from( $limit );

		if( $equal and $this->value < $limit ) {
			$this->error( Error::DATE_VAL_GTE, ['min'=> $limit ]);
		} elseif( !$equal and $this->value <= $limit ) {
			$this->error( Error::DATE_VAL_GT, ['min'=> $limit ]);
		}

		return $this;
	}

	/**
	 * @param mixed $min
	 * @param mixed $max
	 * @return $this
	 */
	function range( $min, $max )
	{
		$min = DateTime::from( $min );
		$max = DateTime::from( $max );

		if( $this->value < $min or $this->value > $max ) {
			$this->error( Error::DATE_VAL_RNG, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}
}