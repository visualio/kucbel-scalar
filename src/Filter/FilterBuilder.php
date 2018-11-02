<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class FilterBuilder
{
	use SmartObject;

	/**
	 * @var FilterInterface[] | null
	 */
	protected $filters;

	/**
	 * FilterBuilder constructor.
	 *
	 * @param FilterInterface[] $filters
	 */
	function __construct( array $filters = null )
	{
		$this->filters = $filters;
	}

	/**
	 * @return FilterFactory
	 */
	function create() : FilterFactory
	{
		return new FilterFactory( $this->filters );
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function push( FilterInterface ...$filters )
	{
		$this->filters = array_merge( $this->filters ?? [], $filters );

		return $this;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return $this
	 */
	function shift( FilterInterface ...$filters )
	{
		$this->filters = array_merge( $filters, $this->filters ?? [] );

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