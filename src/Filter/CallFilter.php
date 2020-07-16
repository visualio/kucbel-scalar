<?php

namespace Kucbel\Scalar\Filter;

use Nette\SmartObject;

class CallFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @var callable
	 */
	protected $callback;

	/**
	 * @var callable
	 */
	protected $condition;

	/**
	 * CallFilter constructor.
	 *
	 * @param callable $callback
	 * @param callable ...$conditions
	 */
	function __construct( callable $callback, callable ...$conditions )
	{
		$this->callback = $callback;
		$this->condition = self::merge( ...$conditions );
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( $value )
	{
		if(( $this->condition )( $value )) {
			$value = ( $this->callback )( $value );
		}

		return $value;
	}

	/**
	 * @param callable ...$conditions
	 * @return callable
	 */
	static function merge( callable ...$conditions ) : callable
	{
		if( isset( $conditions[1] )) {
			return function( $value ) use( $conditions ) {
				foreach( $conditions as $condition ) {
					if( $condition( $value )) {
						return true;
					}
				}

				return false;
			};
		} elseif( isset( $conditions[0] )) {
			return $conditions[0];
		} else {
			return function() {
				return true;
			};
		}
	}
}
