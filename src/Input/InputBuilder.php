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
	 * @var array
	 */
	protected $inputs;

	/**
	 * @var array
	 */
	protected $filters;

	/**
	 * @var array
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
	function __construct( FilterBuilder $builder, array $inputs, array $filters, array $adapters )
	{
		$this->builder = $builder;
		$this->inputs = $inputs;
		$this->filters = $filters;
		$this->adapters = $adapters;
	}

	/**
	 * @return InputFactory
	 */
	function close() : InputFactory
	{
		return new InputFactory( $this->builder->close(), $this->inputs, $this->filters, $this->adapters );
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
	 * @param int $mode
	 * @return $this
	 */
	function filter( int $type, int $mode )
	{
		$filters = self::split( $type, $mode );

		$this->filters = $filters + $this->filters;

		return $this;
	}

	/**
	 * @param int $type
	 * @param int $mode
	 * @return $this
	 */
	function adapter( int $type, int $mode )
	{
		$adapters = self::split( $type, $mode );

		$this->adapters = $adapters + $this->adapters;

		return $this;
	}

	/**
	 * @param int $type
	 * @param int $mode
	 * @return array
	 */
	static function split( int $type, int $mode ) : array
	{
		if( $type <= 0 ) {
			throw new InvalidArgumentException("Invalid type flag.");
		} elseif( $mode < 0 ) {
			throw new InvalidArgumentException("Invalid mode flag.");
		}

		$list = [];

		for( $i = 1; $i <= $type; $i *= 2 ) {
			if( $type & $i ) {
				$list[ $i ] = $mode;
			}
		}

		return $list;
	}
}