<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;

class InputList extends InputAdapter
{
	/**
	 * @param string $name
	 * @return MixedValidator
	 */
	function create( string $name ) : MixedValidator
	{
		return new MixedValidator( $name, $this->get( $name ));
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		$value = null;

		foreach( $this->inputs as $input ) {
			$each = $input->get( $name );

			if( $this->mode & self::CHECK ) {
				if( $each !== null ) {
					$value[] = $each;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $each !== null or $input->has( $name )) {
					$value[] = $each;
				}
			} else {
				$value[] = $each;
			}
		}

		return $value;
	}
}