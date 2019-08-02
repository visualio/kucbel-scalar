<?php

namespace Kucbel\Scalar\Output;

use Kucbel\Scalar\Filter\FilterInterface;
use Nette\SmartObject;

class OutputFilter implements OutputInterface
{
	use SmartObject;

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
