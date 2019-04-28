<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class FilterBuilder
{
	use SmartObject;

	/**
	 * @var FilterInterface[]
	 */
	protected $filters;

	/**
	 * FilterBuilder constructor.
	 *
	 * @param FilterInterface ...$filters
	 */
	function __construct( FilterInterface ...$filters )
	{
		$this->filters = $filters;
	}

	/**
	 * @return FilterFactory
	 */
	function close() : FilterFactory
	{
		return new FilterFactory( ...$this->filters );
	}

	/**
	 * @param string $type
	 * @param string ...$types
	 * @return $this
	 */
	function keep( string $type, string ...$types )
	{
		$types[] = $type;
		$filters = [];

		foreach( $this->filters as $filter ) {
			foreach( $types as $type ) {
				if( $filter instanceof $type ) {
					$filters[] = $filter;

					continue 2;
				}
			}
		}

		$this->filters = $filters;

		return $this;
	}

	/**
	 * @param string $type
	 * @param string ...$types
	 * @return $this
	 */
	function drop( string $type, string ...$types )
	{
		$types[] = $type;
		$filters = [];

		foreach( $this->filters as $filter ) {
			foreach( $types as $type ) {
				if( $filter instanceof $type ) {
					continue 2;
				}
			}

			$filters[] = $filter;
		}

		$this->filters = $filters;

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function push( FilterInterface ...$filters )
	{
		$this->filters = array_merge( $this->filters, $filters );

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function shift( FilterInterface ...$filters )
	{
		$this->filters = array_merge( $filters, $this->filters );

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function reset( FilterInterface ...$filters )
	{
		$this->filters = $filters;

		return $this;
	}
}