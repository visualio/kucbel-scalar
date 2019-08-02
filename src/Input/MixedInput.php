<?php

namespace Kucbel\Scalar\Input;

use Nette\FileNotFoundException;
use Nette\Neon\Exception as NeonException;
use Nette\Neon\Neon;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class MixedInput extends StrictInput implements DetectInterface
{
	use SearchMethod, SectionMethod;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @var string | null
	 */
	protected $alias;

	/**
	 * MixedInput constructor.
	 *
	 * @param mixed $value
	 * @param string $alias
	 */
	function __construct( $value, string $alias = null )
	{
		$this->value = $value;
		$this->alias = self::suffix( $alias );
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
	{
		$name = $this->section . $name;
		$value = $this->value;

		return $this->search( $name, $value ) ? $value : $null;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function alias( string $name ) : string
	{
		return $this->alias . $this->section . $name;
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool
	{
		return is_array( $source ) or is_object( $source );
	}

	/**
	 * @param string $neon
	 * @param string $alias
	 * @return static
	 * @throws NeonException
	 */
	static function neon( string $neon, string $alias = null ) : self
	{
		if( !$neon ) {
			throw new NeonException('Input is empty.');
		}

		return new static( Neon::decode( $neon ), $alias );
	}

	/**
	 * @param string $json
	 * @param string $alias
	 * @return static
	 * @throws JsonException
	 */
	static function json( string $json, string $alias = null ) : self
	{
		if( !$json ) {
			throw new JsonException('Input is empty.');
		}

		return new static( Json::decode( $json ), $alias );
	}

	/**
	 * @param string $file
	 * @param string $alias
	 * @return MixedInput
	 * @throws JsonException
	 * @throws NeonException
	 */
	static function file( string $file, string $alias = null ) : self
	{
		$read = strrpos( $file, '.');

		if( !$read ) {
			throw new InputException("File '{$file}' doesn't have an extension.");
		}

		$type = substr( $file, $read + 1 );
		$data = @file_get_contents( $file );

		if( $data === false ) {
			throw new FileNotFoundException("File '{$file}' isn't readable.");
		}

		switch( $type ) {
			case 'neon':
				return self::neon( $data, $alias );
			case 'json':
				return self::json( $data, $alias );
			default:
				throw new InputException("File '{$file}' isn't supported.");
		}
	}
}
