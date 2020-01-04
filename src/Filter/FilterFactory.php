<?php

namespace Kucbel\Scalar\Filter;

use Kucbel\Scalar\Input\InputFilter;
use Kucbel\Scalar\Input\InputInterface;
use Nette\SmartObject;

class FilterFactory
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
	function __construct( array $filters = null )
	{
		$this->filters = $filters ?? [];
	}

	/**
	 * @param InputInterface $input
	 * @return InputInterface
	 */
	function create( InputInterface $input ) : InputInterface
	{
		if( isset( $this->filters[1] )) {
			return new InputFilter( $input, new FilterPool( ...$this->filters ));
		} elseif( isset( $this->filters[0] )) {
			return new InputFilter( $input, $this->filters[0] );
		} else {
			return $input;
		}
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function value( $value )
	{
		foreach( $this->filters as $filter ) {
			$value = $filter->clear( $value );
		}

		return $value;
	}

	/**
	 * @return FilterBuilder
	 */
	function setup() : FilterBuilder
	{
		return new FilterBuilder( $this->filters );
	}
}
