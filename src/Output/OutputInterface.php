<?php

namespace Kucbel\Scalar\Output;

interface OutputInterface
{
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	function set( string $name, $value );
}