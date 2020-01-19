<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Validator\MixedValidator;

class InputList extends Adapter
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
		$values = null;

		foreach( $this->inputs as $input ) {
			if( $this->mode & self::CHECK ) {
				if(( $value = $input->get( $name )) !== null ) {
					$values[] = $value;
				}
			} elseif( $this->mode & self::QUERY ) {
				if( $input->has( $name )) {
					$values[] = $input->get( $name );
				}
			} else {
				$values[] = $input->get( $name );
			}
		}

		return $values;
	}
}
