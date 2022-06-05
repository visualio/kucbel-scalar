<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;

interface InputInterface
{
	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator;

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name ) : mixed;

	/**
	 * @param string $name
	 * @return bool
	 */
	function has( string $name ) : bool;
}
