<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;
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
	 * @param mixed ...$values
	 * @return $this
	 */
	function equal( ...$values )
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$match = false;

		foreach( $values as &$value ) {
			$value = DateTime::from( $value );

			if( $this->value == $value ) {
				$match = true;

				break;
			}
		}

		if( !$match ) {
			$text = isset( $values[1] ) ? 'one of the following' : 'equal to';

			$this->error("Parameter \$name must be {$text} \$list.", Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param mixed|null $lower limit
	 * @param mixed|null $upper limit
	 * @param int $flag
	 * @return $this
	 */
	function value( $lower, $upper, int $flag = 0 )
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$match = true;

		if( $lower !== null ) {
			$lower = DateTime::from( $lower );

			if(( $this->value <=> $lower ) <= ( $flag & self::EXCL_LOWER ? 0 : -1 )) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			$upper = DateTime::from( $upper );

			if(( $this->value <=> $upper ) >= ( $flag & self::EXCL_UPPER ? 0 : 1 )) {
				$match = false;
			}
		}

		if( !$match ) {
			$text = '';

			if( $lower !== null ) {
				$text .= $flag & self::EXCL_LOWER ? " greater than \$lower" : " equal or greater than \$lower";
			}

			if( $lower !== null and $upper !== null ) {
				$text .= " and";
			}

			if( $upper !== null ) {
				$text .= $flag & self::EXCL_UPPER ? " less than \$upper" : " equal or less than \$upper";
			}

			$this->error("Parameter \$name must be{$text}.", Error::MIX_VALUE, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}
}
