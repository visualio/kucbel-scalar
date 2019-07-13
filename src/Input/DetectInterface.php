<?php

namespace Kucbel\Scalar\Input;

interface DetectInterface extends InputInterface
{
	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( $source ) : bool;
}
