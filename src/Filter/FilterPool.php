<?php

namespace Kucbel\Scalar\Filter;

class FilterPool extends Filter
{
	/**
	 * @var FilterInterface[]
	 */
	private $filters;

	/**
	 * FilterFactory constructor.
	 *
	 * @param FilterInterface[] $filters
	 */
	function __construct( FilterInterface ...$filters )
	{
		$this->filters = $filters;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( $value )
	{
		foreach( $this->filters as $filter ) {
			if( $value === null ) {
				break;
			}

			$value = $filter->clear( $value );
		}

		return $value;
	}
}