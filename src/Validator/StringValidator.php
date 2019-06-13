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
			throw new InvalidArgumentException("Enter at least one parameter.");
		}

		if( !in_array( $this->value, $values, true )) {
			throw new ValidatorException( $this->name, Error::SCA_EQUAL, ['val' => $values ]);
		}

		return $this;
	}

	/**
	 * @param int|null $min
	 * @param int|null $max
	 * @return $this
	 */
	function length( ?int $min, ?int $max = 1 )
	{
		return $this->char( $min, $max );
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
				throw new InvalidArgumentException("Enter positive length limit.");
			}

			if( $len < $min ) {
				throw new ValidatorException( $this->name, Error::STR_CHAR, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 0 ) {
				throw new InvalidArgumentException("Enter positive length limit.");
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
				throw new InvalidArgumentException("Enter positive count limit.");
			}

			if( $num < $min ) {
				throw new ValidatorException( $this->name, Error::STR_LINE, ['min' => $min, 'max' => $max ]);
			}
		}

		if( $max !== null ) {
			if( $max < 1 ) {
				throw new InvalidArgumentException("Enter positive count limit.");
			}

			if( $num > $max ) {
				throw new ValidatorException( $this->name, Error::STR_LINE, ['min' => $min, 'max' => $max ]);
			}
		}

		return $this;
	}

	/**
	 * @param string $type
	 * @param bool $same
	 * @return $this
	 */
	function impl( string $type, bool $same = false )
	{
		$value = ltrim( $this->value, '\\');

		if( !class_exists( $value ) or (( $same and !is_a( $value, $type, true )) or ( !$same and !is_subclass_of( $value, $type, true )))) {
			throw new ValidatorException( $this->name, Error::STR_IMPL, ['type' => $type ]);
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
	function dir( bool $real = false )
	{
		if( !is_dir( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_DIR );
		}

		$this->path( $real, false );

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = false )
	{
		if( !is_file( $this->value )) {
			throw new ValidatorException( $this->name, Error::STR_FILE );
		}

		$this->path( $real, true );

		return $this;
	}

	/**
	 * @param bool $real
	 * @param bool $file
	 */
	protected function path( bool $real, bool $file )
	{
		if( $real ) {
			$value = realpath( $this->value );

			if( $value === false ) {
				throw new ValidatorException( $this->name, $file ? Error::STR_FILE : Error::STR_DIR );
			}

			$this->value = $value;
		}

		$this->value = str_replace('\\', '/', $this->value );
	}
}
