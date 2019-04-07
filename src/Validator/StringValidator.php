<?php

namespace Kucbel\Scalar\Validator;

use Kucbel\Scalar\Error;
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
	 * @param string ...$options
	 * @return $this
	 */
	function equal( string ...$options )
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
	 * @return $this
	 */
	function max( int $limit )
	{
		$length = Strings::length( $this->value );

		if( $length > $limit ) {
			$this->error( Error::STR_LEN_LTE, ['max' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function min( int $limit )
	{
		$length = Strings::length( $this->value );

		if( $length < $limit ) {
			$this->error( Error::STR_LEN_GTE, ['min' => $limit ]);
		}

		return $this;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function length( int $min, int $max = null )
	{
		$length = Strings::length( $this->value );

		if( $min === $max or $max === null ) {
			$equal = true;
		} else {
			$equal = false;
		}

		if( $equal and $length !== $min ) {
			$this->error( Error::STR_LEN_EQ, ['len' => $min ]);
		} elseif( !$equal and ( $length < $min or $length > $max )) {
			$this->error( Error::STR_LEN_RNG, ['min' => $min, 'max' => $max ]);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function email()
	{
		if( !Validators::isEmail( $this->value )) {
			$this->error( Error::STR_EMAIL );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url()
	{
		if( !Validators::isUrl( $this->value )) {
			$this->error( Error::STR_URL );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function line()
	{
		if( Strings::match( $this->value, '~\R~u')) {
			$this->error( Error::STR_LINE );
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
			$this->error( Error::STR_DIR );
		}

		if( $real ) {
			$this->path( Error::STR_DIR );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = false )
	{
		if( !is_file( $this->value )) {
			$this->error( Error::STR_FILE );
		}

		if( $real ) {
			$this->path( Error::STR_FILE );
		}

		return $this;
	}

	/**
	 * @param int $code
	 */
	protected function path( int $code )
	{
		$value = realpath( $this->value );

		if( $value === false ) {
			$this->error( $code );
		}

		$this->value = str_replace('\\', '/', $value );
	}

	/**
	 * @param string $class
	 * @param bool $equal
	 * @return $this
	 */
	function impl( string $class, bool $equal = false )
	{
		$value = ltrim( $this->value, '\\');

		if( !class_exists( $value ) or !(( $equal and is_a( $value, $class, true )) or ( !$equal and is_subclass_of( $value, $class, true )))) {
			if( interface_exists( $class )) {
				$this->error( Error::STR_INTER, ['inter' => $class ]);
			} else {
				$this->error( Error::STR_CLASS, ['class' => $class ]);
			}
		}

		$this->value = $value;

		return $this;
	}
}