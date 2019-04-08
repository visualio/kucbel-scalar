<?php

namespace Kucbel\Scalar\Validator;
use Kucbel\Scalar\Error;

/**
 * Class FloatValidator
 *
 * @method float fetch()
 */
class FloatValidator extends NumericValidator
{
	/**
	 * FloatValidator constructor.
	 *
	 * @param string $name
	 * @param float $value
	 */
	function __construct( string $name, float $value )
	{
		$this->name = $name;
		$this->value = $value ? $value : abs( $value );
	}

	/**
	 * @param float ...$options
	 * @return $this
	 */
	function equal( float ...$options )
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
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function max( float $limit, bool $equal = true )
	{
		if( $equal and $this->value > $limit ) {
			$this->error( Error::NUM_VAL_LTE, ['max' => $limit ]);
		} elseif( !$equal and $this->value >= $limit ) {
			$this->error( Error::NUM_VAL_LT, ['max' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param float $limit
	 * @param bool $equal
	 * @return $this
	 */
	function min( float $limit, bool $equal = true )
	{
		if( $equal and $this->value < $limit ) {
			$this->error( Error::NUM_VAL_GTE, ['min' => $limit ]);
		} elseif( !$equal and $this->value <= $limit ) {
			$this->error( Error::NUM_VAL_GT, ['min' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param float $min
	 * @param float $max
	 * @return $this
	 */
	function range( float $min, float $max )
	{
		if( $this->value < $min or $this->value > $max ) {
			$this->error( Error::NUM_VAL_RNG, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param int $digit
	 * @param int $point
	 * @return $this
	 */
	function length( int $digit, int $point )
	{
		return $this->digit( $digit )->point( $point );
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	protected function point( int $limit )
	{
		if( $this->value !== round( $this->value, $limit )) {
			$this->error( Error::NUM_POINT, ['dec' => $limit ]);
		}

		return $this;
	}
}