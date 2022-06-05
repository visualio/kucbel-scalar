<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * Class StringValidator
 *
 * @method string fetch()
 */
class StringValidator extends ScalarValidator
{
	/**
	 * StringValidator constructor.
	 *
	 * @param string $name
	 * @param string $value
	 */
	function __construct( string $name, string $value )
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @param string ...$values
	 * @return $this
	 */
	function equal( string ...$values ) : static
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( !in_array( $this->value, $values, true )) {
			$text = isset( $values[1] ) ? 'one of the following' : 'equal to';

			$this->error("Parameter \$name must be {$text} \$list.", Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $lower limit
	 * @param int|null $upper limit
	 * @return $this
	 */
	function char( ?int $lower, ?int $upper ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$check = Strings::length( $this->value );
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

			$this->error("Parameter \$name must be {$text} characters long.", Error::STR_CHAR, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @param int|null $lower
	 * @param int|null $upper
	 * @return $this
	 */
	function line( ?int $lower, ?int $upper ) : static
	{
		if( $lower === null and $upper === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$check = count( Strings::matchAll( $this->value, '~\r\n|\r|\n~')) + 1;
		$match = true;

		if( $lower !== null ) {
			if( $lower < 1 ) {
				throw new InvalidArgumentException("Enter a positive non-zero lower limit.");
			}

			if( $check < $lower ) {
				$match = false;
			}
		}

		if( $upper !== null ) {
			if( $upper < 1 ) {
				throw new InvalidArgumentException("Enter a positive non-zero upper limit.");
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

			$this->error("Parameter \$name must have {$text} lines.", Error::STR_LINE, ['lower' => $lower, 'upper' => $upper ]);
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function class( string $type ) : static
	{
		$check = ltrim( $this->value, '\\');

		if( !class_exists( $check ) or !is_subclass_of( $check, $type, true )) {
			$text = interface_exists( $type ) ? "implementing \$type interface" : "extending \$type parent";

			$this->error("Parameter \$name must be a class {$text}.", Error::STR_CLASS, ['type' => $type ]);
		}

		$this->value = $check;

		return $this;
	}

	/**
	 * @return $this
	 */
	function email() : static
	{
		if( !Validators::isEmail( $this->value )) {
			$this->error("Parameter \$name must be an email.", Error::STR_EMAIL );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url() : static
	{
		if( !Validators::isUrl( $this->value )) {
			$this->error("Parameter \$name must be an url.", Error::STR_URL );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = true ) : static
	{
		if( !is_file( $this->value ) or !$this->path( $real )) {
			$this->error("Parameter \$name must point to a file.", Error::STR_FILE );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function folder( bool $real = true ) : static
	{
		if( !is_dir( $this->value ) or !$this->path( $real )) {
			$this->error("Parameter \$name must point to a folder.", Error::STR_FOLDER );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return bool
	 */
	protected function path( bool $real ) : bool
	{
		$check = $this->value;

		if( $real ) {
			$check = realpath( $check );

			if( $check === false ) {
				return false;
			}
		}

		$this->value = str_replace('\\', '/', $check );

		return true;
	}
}
