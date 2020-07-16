<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Filter\FilterInterface;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class InputFilter implements InputInterface
{
	use SmartObject;

	const
		QUERY	= 0b1,
		CHECK	= 0b10;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var FilterInterface
	 */
	protected $filter;

	/**
	 * @var bool
	 */
	protected $check = false;

	/**
	 * InputFilter constructor.
	 *
	 * @param InputInterface $input
	 * @param FilterInterface $filter
	 * @param int $mode
	 */
	function __construct( InputInterface $input, FilterInterface $filter, int $mode = null )
	{
		$this->input = $input;
		$this->filter = $filter;

		if( $mode and $mode & self::CHECK ) {
			$this->check = true;
		}
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
		if( $this->check ) {
			return $this->filter->clear( $this->input->get( $name )) !== null;
		} else {
			return $this->input->has( $name );
		}
	}

	/**
	 * @param int $mode
	 */
	function mode( int $mode )
	{
		if( $mode & self::CHECK ) {
			$this->check = true;
		} else {
			$this->check = false;
		}
	}
}
