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

		if( $this->mode & self::MERGE ) {
			return $values ? $this->merge( $values ) : $values;
		} else {
			return $values;
		}
	}

	/**
	 * @param array $values
	 * @return array
	 */
	protected function merge( array $values ) : array
	{
		$merges = null;

		foreach( $values as $value ) {
			if( is_iterable( $value )) {
				foreach( $value as $each ) {
					$merges[] = $each;
				}
			} else {
				$merges[] = $value;
			}
		}

		return $merges;
	}
}
