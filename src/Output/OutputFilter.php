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
	protected $output;

	/**
	 * @var FilterInterface
	 */
	protected $filter;

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
	function set( string $name, mixed $value )
	{
		$this->output->set( $name, $this->filter->clear( $value ));
	}
}
