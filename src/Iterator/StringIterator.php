<?php

namespace Kucbel\Scalar\Iterator;

use Kucbel\Scalar\Validator\StringValidator;

/**
 * Class StringIterator
 *
 * @method StringValidator item( int $index )
 * @method StringValidator first()
 * @method StringValidator last()
 * @method StringValidator current()
 *
 * @method string[] fetch()
 */
class StringIterator extends ScalarIterator
{
	/**
	 * StringIterator constructor.
	 *
	 * @param string $name
	 * @param StringValidator ...$list
	 */
	function __construct( string $name, StringValidator ...$list )
	{
		$this->name = $name;
		$this->list = $list;
	}

	/**
	 * @param string ...$options
	 * @return $this
	 */
	function equal( string ...$options )
	{
		return $this->each('equal', ...$options );
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function max( int $limit )
	{
		return $this->each('max', $limit );
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function min( int $limit )
	{
		return $this->each('min', $limit );
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function length( int $min, int $max = null )
	{
		return $this->each('length', $min, $max );
	}

	/**
	 * @return $this
	 */
	function email()
	{
		return $this->each('email');
	}

	/**
	 * @return $this
	 */
	function url()
	{
		return $this->each('url');
	}

	/**
	 * @return $this
	 */
	function line()
	{
		return $this->each('line');
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function dir( bool $real = false )
	{
		return $this->each('dir', $real );
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = false )
	{
		return $this->each('file', $real );
	}

	/**
	 * @param string $class
	 * @param bool $equal
	 * @return $this
	 */
	function impl( string $class, bool $equal = false )
	{
		return $this->each('impl', $class, $equal );
	}
}