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
		foreach( $this->list as $item ) {
			$item->equal( ...$options );
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function max( int $limit )
	{
		foreach( $this->list as $item ) {
			$item->max( $limit );
		}

		return $this;
	}

	/**
	 * @param int $limit
	 * @return $this
	 */
	function min( int $limit )
	{
		foreach( $this->list as $item ) {
			$item->min( $limit );
		}

		return $this;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	function length( int $min, int $max = null )
	{
		foreach( $this->list as $item ) {
			$item->length( $min, $max );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function email()
	{
		foreach( $this->list as $item ) {
			$item->email();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function url()
	{
		foreach( $this->list as $item ) {
			$item->url();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	function line()
	{
		foreach( $this->list as $item ) {
			$item->line();
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function dir( bool $real = false )
	{
		foreach( $this->list as $item ) {
			$item->dir( $real );
		}

		return $this;
	}

	/**
	 * @param bool $real
	 * @return $this
	 */
	function file( bool $real = false )
	{
		foreach( $this->list as $item ) {
			$item->file( $real );
		}

		return $this;
	}

	/**
	 * @param string $class
	 * @param bool $equal
	 * @return $this
	 */
	function impl( string $class, bool $equal = false )
	{
		foreach( $this->list as $item ) {
			$item->impl( $class, $equal );
		}

		return $this;
	}
}