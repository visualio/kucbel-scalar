<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;

/**
 * Class DecimalValidator
 *
 * @method string fetch()
 */
class DecimalValidator extends ScalarValidator
{
	/**
	 * DecimalValidator constructor.
	 *
	 * @param string $name
	 * @param string $value
	 */
	function __construct( string $name, string $value )
	{
		$value = ltrim( $value, '+');
		$value = ltrim( $value, '0');

		if( $value === '') {
			$value = '0';
		} elseif( str_starts_with( $value, '.')) {
			$value = "0{$value}";
		}

		if( str_contains( $value, '.')) {
			$value = rtrim( $value, '0');
			$value = rtrim( $value, '.');
		}

		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param string|int|null $lower limit
	 * @param string|int|null $upper limit
	 * @param int $flag
	 * @return $this
	 */
	function value( string | int | null $lower, string | int | null $upper, int $flag = 0 ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$match = true;

		if( $lower !== null ) {
			if( $this->compare( $this->value, $flag & self::EXCL_LOWER ? '<' : '<=', $lower )) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $this->compare( $this->value, $flag & self::EXCL_UPPER ? '>' : '>=', $upper )) {
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

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function digit( ?int $lower, ?int $upper ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$split = explode('.', $this->value, 2 );
		$check = strlen( ltrim( $split[0], '-'));
		$match = true;

		if( $lower !== null and $lower !== 0 ) {
			if( $lower < 0 ) {
				throw new InvalidArgumentException("Enter a positive lower limit.");
			}

			if( $check < $lower ) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 0 ) {
				throw new InvalidArgumentException("Enter a positive upper limit.");
			}

			if( $check > $upper ) {
				$match = false;
			}
		}

		if( !$match ) {
			if( $lower === $upper ) {
				$text = "exactly \$lower";
			} elseif( $upper === null ) {
				$text = "at least \$lower";
			} elseif( $lower === null ) {
				$text = "at most \$upper";
			} else {
				$text = "between \$lower and \$upper";
			}

			$this->error("Parameter \$name must have {$text} digits.", Error::NUM_DIGIT, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function point( ?int $lower, ?int $upper ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$split = explode('.', $this->value, 2 );
		$check = isset( $split[1] ) ? strlen( rtrim( $split[1], '0')) : 0;
		$match = true;

		if( $lower !== null and $lower !== 0 ) {
			if( $lower < 0 ) {
				throw new InvalidArgumentException("Enter a positive lower limit.");
			}

			if( $check < $lower ) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 0 ) {
				throw new InvalidArgumentException("Enter a positive upper limit.");
			}

			if( $check > $upper ) {
				$match = false;
			}
		}

		if( !$match ) {
			if( $lower === $upper ) {
				$text = "exactly \$lower";
			} elseif( $upper === null ) {
				$text = "at least \$lower";
			} elseif( $lower === null ) {
				$text = "at most \$upper";
			} else {
				$text = "between \$lower and \$upper";
			}

			$this->error("Parameter \$name must have {$text} decimal digits.", Error::NUM_POINT, ['lower' => $lower, 'upper' => $upper ]);
		}

		$reset = $upper ?? $lower;

		if( $reset > 0 and $check < $reset ) {
			$split[1] = str_pad( $split[1] ?? '', $reset, '0', STR_PAD_RIGHT );

			$this->value = implode('.', $split );
		}

		return $this;
	}

	/**
	 * @param string | int $left
	 * @param string $mode
	 * @param string | int $right
	 * @return bool
	 */
	protected function compare( string | int $left, string $mode, string | int $right ) : bool
	{
		$equal = bccomp( (string) $left, (string) $right, $this->scale( $left, $right ));

		switch( $mode ) {
			case '>'  : return $equal  >  0;
			case '>=' : return $equal  >= 0;
			case '<'  : return $equal  <  0;
			case '<=' : return $equal  <= 0;
			case  '=' : return $equal === 0;
			case '!=' : return $equal !== 0;
			default:
				throw new InvalidArgumentException('Wrong mode.');
		}
	}

	/**
	 * @param string | int ...$values
	 * @return int
	 */
	protected function scale( string | int ...$values ) : int
	{
		$scale = 0;

		foreach( $values as $value ) {
			if( is_int( $value )) {
				continue;
			}

			$split = explode('.', $value, 2 );
			$limit = isset( $split[1] ) ? strlen( $split[1] ) : 0;

			if( $scale < $limit ) {
				$scale = $limit;
			}
		}

		return $scale;
	}
}