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
	 * @param mixed $value
	 * @return mixed
	 */
	function value( $value )
	{
		foreach( $this->filters as $filter ) {
			$value = $filter->value( $value );
		}

		return $value;
	}
}
