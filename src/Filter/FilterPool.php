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
	function clear( mixed $value ) : mixed
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
