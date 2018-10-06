<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Filter\FilterInterface;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class InputFilter implements InputInterface
{
	use SmartObject;

	const
		NONE	= 0b0,
		WRAP	= 0b1,
		EACH	= 0b10;

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * @var FilterInterface
	 */
	private $filter;

	/**
	 * FilterInput constructor.
	 *
	 * @param InputInterface $input
	 * @param FilterInterface $filter
	 */
	function __construct( InputInterface $input, FilterInterface $filter )
	{
		$this->input = $input;
		$this->filter = $filter;
	}

	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		$mixed = $this->input->create( $name );
		$mixed->clear( $this->filter );

		return $mixed;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		return $this->filter->clear( $this->input->get( $name ));
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool
	{
		return $this->input->has( $name );
	}
}