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
		if( isset( $this->filters[0] )) {
			return new InputFilter( $input, $this->get() );
		} else {
			return $input;
		}
	}

	/**
	 * @return FilterInterface
	 */
	function get() : FilterInterface
	{
		if( isset( $this->filters[1] )) {
			return new FilterPool( ...$this->filters );
		} elseif( isset( $this->filters[0] )) {
			return $this->filters[0];
		} else {
			return new VoidFilter;
		}
	}

	/**
	 * @return bool
	 */
	function has() : bool
	{
		return isset( $this->filters[0] );
	}

	/**
	 * @return FilterBuilder
	 */
	function setup() : FilterBuilder
	{
		return new FilterBuilder( $this->filters );
	}
}
