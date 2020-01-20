<?php

namespace Kucbel\Scalar\Filter;

use Kucbel\Scalar\Input\InputFilter;
use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Output\OutputFilter;
use Kucbel\Scalar\Output\OutputInterface;
use Nette\SmartObject;

class FilterFactory
{
	use SmartObject;

	/**
	 * @var FilterInterface[]
	 */
	protected $filters;

	/**
	 * @var FilterInterface | null
	 */
	protected $filter;

	/**
	 * FilterFactory constructor.
	 *
	 * @param FilterInterface ...$filters
	 */
	function __construct( FilterInterface ...$filters )
	{
		$this->filters = $filters;
	}

	/**
	 * FilterFactory cloner.
	 */
	function __clone()
	{
		$this->filter = null;
	}

	/**
	 * @return FilterInterface
	 */
	protected function get() : FilterInterface
	{
		if( $this->filter ) {
			return $this->filter;
		} elseif( isset( $this->filters[1] )) {
			return $this->filter = new FilterPool( ...$this->filters );
		} elseif( isset( $this->filters[0] )) {
			return $this->filter = $this->filters[0];
		} else {
			return $this->filter = new VoidFilter;
		}
	}

	/**
	 * @param InputInterface $input
	 * @return InputInterface
	 */
	function input( InputInterface $input ) : InputInterface
	{
		if( $this->filters ) {
			return new InputFilter( $input, $this->get() );
		} else {
			return $input;
		}
	}

	/**
	 * @param OutputInterface $output
	 * @return OutputInterface
	 */
	function output( OutputInterface $output ) : OutputInterface
	{
		if( $this->filters ) {
			return new OutputFilter( $output, $this->get() );
		} else {
			return $output;
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
	 * @param FilterInterface ...$filters
	 * @return FilterFactory
	 */
	function withFirst( FilterInterface ...$filters ) : self
	{
		$that = clone $this;
		$that->filters = $filters;

		foreach( $this->filters as $filter ) {
			$that->filters[] = $filter;
		}

		return $that;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return FilterFactory
	 */
	function withLast( FilterInterface ...$filters ) : self
	{
		$that = clone $this;

		foreach( $filters as $filter ) {
			$that->filters[] = $filter;
		}

		return $that;
	}

	/**
	 * @param FilterInterface ...$filters
	 * @return FilterFactory
	 */
	function withOnly( FilterInterface ...$filters ) : self
	{
		$that = clone $this;
		$that->filters = $filters;

		return $that;
	}

	/**
	 * @param string $type
	 * @param string ...$types
	 * @return FilterFactory
	 */
	function withType( string $type, string ...$types ) : self
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

		return $this->withOnly( ...$filters );
	}

	/**
	 * @param string $type
	 * @param string ...$types
	 * @return FilterFactory
	 */
	function withoutType( string $type, string ...$types ) : self
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

		return $this->withOnly( ...$filters );
	}
}
