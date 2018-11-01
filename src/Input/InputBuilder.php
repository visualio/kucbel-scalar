<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Filter\FilterBuilder;
use Kucbel\Scalar\Filter\FilterInterface;
use Nette\InvalidArgumentException;
use Nette\SmartObject;

class InputBuilder
{
	use SmartObject;

	/**
	 * @var FilterBuilder
	 */
	protected $builder;

	/**
	 * @var array | null
	 */
	protected $inputs;

	/**
	 * @var array | null
	 */
	protected $filters;

	/**
	 * @var array | null
	 */
	protected $adapters;

	/**
	 * InputBuilder constructor.
	 *
	 * @param FilterBuilder $builder
	 * @param array $inputs
	 * @param array $filters
	 * @param array $adapters
	 */
	function __construct( FilterBuilder $builder, array $inputs = null, array $filters = null, array $adapters = null )
	{
		$this->builder = $builder;
		$this->inputs = $inputs;
		$this->filters = $filters;
		$this->adapters = $adapters;
	}

	/**
	 * @return InputFactory
	 */
	function create() : InputFactory
	{
		return new InputFactory( $this->builder->create(), $this->inputs, $this->filters, $this->adapters );
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function push( FilterInterface ...$filters )
	{
		$this->builder->push( ...$filters );

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function shift( FilterInterface ...$filters )
	{
		$this->builder->shift( ...$filters );

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function reset( FilterInterface ...$filters )
	{
		$this->builder->reset( ...$filters );

		return $this;
	}

	/**
	 * @param int $type
	 * @param int $flag
	 * @return $this
	 */
	function filter( int $type, int $flag )
	{
		self::check( $flag );

		foreach( self::split( $type ) as $type ) {
			$this->filters[ $type ] = $flag;
		}

		return $this;
	}

	/**
	 * @param int $type
	 * @param int $flag
	 * @return $this
	 */
	function adapter( int $type, int $flag )
	{
		self::check( $flag );

		foreach( self::split( $type ) as $type ) {
			$this->adapters[ $type ] = $flag;
		}

		return $this;
	}

	/**
	 * @param int $flag
	 */
	static function check( int $flag )
	{
		if( $flag < 0 ) {
			throw new InvalidArgumentException("Invalid flag.");
		}
	}

	/**
	 * @param int $type
	 * @return array
	 */
	static function split( int $type ) : array
	{
		if( $type <= 0 ) {
			throw new InvalidArgumentException("Invalid flag.");
		}

		$bin = decbin( $type );
		$len = strlen( $bin );
		$dec = str_repeat('0', $len );
		
		$arr = [];

		for( $i = 0; $i < $len; $i++ ) {
			if( $bin[ $i ] ) {
				$dec[ $i ] = '1';
				$arr[] = bindec( $dec );
				$dec[ $i ] = '0';
			}
		}

		return $arr;
	}
}