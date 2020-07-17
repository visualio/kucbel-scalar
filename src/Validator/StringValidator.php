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
	function equal( string ...$values )
	{
		if( !$values ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		if( !in_array( $this->value, $values, true )) {
			throw new ValidatorException( $this->name, Error::MIX_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function char( ?int $min, ?int $max )
	{
		if(( $min !== null and $min < 0 ) or ( $max !== null and $max < 0 )) {
			throw new InvalidArgumentException("Enter a positive length limit.");
		} elseif( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$len = Strings::length( $this->value );

		if(( $min !== null and $len < $min ) or ( $max !== null and $len > $max )) {
			throw new ValidatorException( $this->name, Error::STR_CHAR, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function line( ?int $min, ?int $max )
	{
		if(( $min !== null and $min < 0 ) or ( $max !== null and $max < 1 )) {
			throw new InvalidArgumentException("Enter a positive non-zero count limit.");
		} elseif( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter at least one value.");
		}

		$num = count( Strings::matchAll( $this->value, '~\r\n|\r|\n~')) + 1;

		if(( $min !== null and $num < $min ) or ( $max !== null and $num > $max )) {
			throw new ValidatorException( $this->name, Error::STR_LINE, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function class( string $type )
	{
		$value = ltrim( $this->value, '\\');

		if( !class_exists( $value ) or !is_subclass_of( $value, $type, true )) {
			throw new ValidatorException( $this->name, Error::STR_CLASS, ['type' => $type ]);
		}

		$this->value = $value;

		return $this;
	}

	/**
	 * @return $this
	 */
	function email()
	{
		if( !Validators::isEmail( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_EMAIL );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url()
	{
		if( !Validators::isUrl( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_URL );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = true )
	{
		if( !is_file( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_FILE );
		} elseif( !$this->path( $real )) {
			throw new ValidatorException( $this->name, Error::STR_FILE );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function folder( bool $real = true )
	{
		if( !is_dir( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_FOLDER );
		} elseif( !$this->path( $real )) {
			throw new ValidatorException( $this->name, Error::STR_FILE );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return bool
	 */
	protected function path( bool $real ) : bool
	{
		$value = $this->value;

		if( $real and ( $value = realpath( $value )) === false ) {
			return false;
		}

		$this->value = str_replace('\\', '/', $value );

		return true;
	}
}
