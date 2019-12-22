<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class FilterPool implements FilterInterface
{
	use SmartObject;

	/**
	 * @var FilterInterface[]
	 */
	protected $filters;

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
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( string $name, $value )
	{
		foreach( $this->filters as $filter ) {
			$value = $filter->clear( $name, $value );
		}

		return $value;
	}
}
