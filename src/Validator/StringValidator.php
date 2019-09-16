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
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['list' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function char( ?int $min, ?int $max = 1 )
	{
		if( $min !== null and $max !== null and $min > $max ) {
			[ $min, $max ] = [ $max, $min ];
		}

		if( $min === 0 ) {
			$min = null;
		}

		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		$len = Strings::length( $this->value );

		if( $min !== null ) {
			if( $min < 0 ) {
				throw new InvalidArgumentException("Enter positive character limit.");
			}

			if( $len < $min ) {
				throw new ValidatorException( $this->name, Error::STR_CHAR, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 0 ) {
				throw new InvalidArgumentException("Enter positive character limit.");
			}

			if( $len > $max ) {
				throw new ValidatorException( $this->name, Error::STR_CHAR, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function line( ?int $min, ?int $max = 1 )
	{
		if( $min !== null and $max !== null and $min > $max ) {
			[ $min, $max ] = [ $max, $min ];
		}

		if( $min === 1 ) {
			$min = null;
		}

		if( $min === null and $max === null ) {
			throw new InvalidArgumentException("Enter value for either one or both parameters.");
		}

		$num = count( Strings::matchAll( $this->value, '~\r\n|\r|\n~')) + 1;

		if( $min !== null ) {
			if( $min < 1 ) {
				throw new InvalidArgumentException("Enter positive line limit.");
			}

			if( $num < $min ) {
				throw new ValidatorException( $this->name, Error::STR_LINE, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 1 ) {
				throw new InvalidArgumentException("Enter positive line limit.");
			}

			if( $num > $max ) {
				throw new ValidatorException( $this->name, Error::STR_LINE, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
	}

	/**
	 * @param int|int[] $char
	 * @param int|int[] $line
	 * @return $this
	 */
	function length( $char, $line )
	{
		$char = self::range( $char );
		$line = self::range( $line );

		return $this->char( ...$char )->line( ...$line );
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
		if( $real ) {
			$value = realpath( $this->value );

			if( $value === false ) {
				return false;
			}

			$this->value = $value;
		}

		$this->value = str_replace('\\', '/', $this->value );

		return true;
	}
}
