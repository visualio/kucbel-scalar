<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class NameFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @var FilterInterface
	 */
	protected $filter;

	/**
	 * @var array
	 */
	protected $names;

	/**
	 * NameFilter constructor.
	 *
	 * @param FilterInterface $filter
	 * @param string ...$names
	 */
	function __construct( FilterInterface $filter, string ...$names )
	{
		$this->filter = $filter;
		$this->names = array_flip( $names );
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( string $name, $value )
	{
		if( isset( $this->names[ $name ] )) {
			$value = $this->filter->clear( $name, $value );
		}

		return $value;
	}
}
