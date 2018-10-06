<?php

namespace Kucbel\Scalar\Output;

use Kucbel\Scalar\Filter\FilterInterface;

class OutputFilter extends Output
{
	/**
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var FilterInterface
	 */
	private $filter;

	/**
	 * OutputFilter constructor.
	 *
	 * @param OutputInterface $output
	 * @param FilterInterface $filter
	 */
	function __construct( OutputInterface $output, FilterInterface $filter )
	{
		$this->output = $output;
		$this->filter = $filter;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value )
	{
		$this->output->set( $name, $this->filter->clear( $value ));
	}
}