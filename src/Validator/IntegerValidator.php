<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;

/**
 * Class IntegerValidator
 *
 * @method int fetch()
 */
class IntegerValidator extends NumericValidator
{
	/**
	 * IntegerValidator constructor.
	 *
	 * @param string $name
	 * @param int $value
	 */
	function __construct( string $name, int $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param int ...$options
	 * @return $this
	 */
	function equal( int ...$options )
	{
		if( !in_array( $this->value, $options, true )) {
			if( isset( $options[1] )) {
				$this->error( Error::SCA_OPTION, ['opt' => $options ]);
			} else {
				$this->error( Error::SCA_EQUAL, ['val' => $options[0] ?? null ]);
			}
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( int $limit, bool $equal = true )
	{
		if( $equal and $this->value > $limit ) {
			$this->error( Error::NUM_VAL_LTE, ['max' => $limit ]);
		} elseif( !$equal and $this->value >= $limit ) {
			$this->error( Error::NUM_VAL_LT, ['max' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( int $limit, bool $equal = true )
	{
		if( $equal and $this->value < $limit ) {
			$this->error( Error::NUM_VAL_GTE, ['min' => $limit ]);
		} elseif( !$equal and $this->value <= $limit ) {
			$this->error( Error::NUM_VAL_GT, ['min' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function range( int $min, int $max )
	{
		if( $this->value < $min or $this->value > $max ) {
			$this->error( Error::NUM_VAL_RNG, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param int $digits
	 * @return $this
	 */
	function length( int $digits )
	{
		return $this->digits( $digits );
	}
}