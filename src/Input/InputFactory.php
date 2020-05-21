<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Filter\FilterFactory;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class InputFactory
{
	use SmartObject;

	/**
	 * @var FilterFactory
	 */
	protected $filter;

	/**
	 * @var DetectInterface[]
	 */
	protected $inputs;

	/**
	 * InputFactory constructor.
	 *
	 * @param FilterFactory $filter
	 * @param string[] $inputs
	 */
	function __construct( FilterFactory $filter, array $inputs = null )
	{
		$this->filter = $filter;
		$this->inputs = $inputs ?? [
			ComponentInput::class,
			RequestInput::class,
			ConsoleInput::class,
			DocumentInput::class,
			ArrayInput::class,
			MixedInput::class,
		];
	}

	/**
	 * @param mixed $source
	 * @param mixed ...$options
	 * @return InputInterface
	 */
	protected function wrap( $source, ...$options ) : InputInterface
	{
		if( $source instanceof InputInterface ) {
			return $source;
		}

		foreach( $this->inputs as $input ) {
			if( $input::handle( $source )) {
				return new $input( $source, ...$options );
			}
		}

		if( is_object( $source )) {
			$type = get_class( $source );
			$what = 'class';
		} else {
			$type = strtolower( gettype( $source ));
			$what = 'type';
		}

		throw new InputException("No input registered for '{$type}' {$what}.");
	}

	/**
	 * @param mixed $source
	 * @param mixed ...$options
	 * @return InputInterface
	 */
	function create( $source, ...$options ) : InputInterface
	{
		$input = $this->wrap( $source, ...$options );

		return $this->filter->input( $input );
	}

	/**
	 * @param mixed $value
	 * @param string $name
	 * @return MixedValidator
	 */
	function value( $value, string $name = '???') : MixedValidator
	{
		$value = $this->filter->value( $value );

		return new MixedValidator( $name, $value );
	}

	/**
	 * @return InputFactory
	 */
	function withoutFilter() : self
	{
		$that = clone $this;
		$that->filter = new FilterFactory;

		return $that;
	}

	/**
	 * @param FilterFactory $filter
	 * @return InputFactory
	 */
	function withFilter( FilterFactory $filter ) : self
	{
		$that = clone $this;
		$that->filter = $filter;

		return $that;
	}

	/**
	 * @param callable $callback
	 * @return InputFactory
	 */
	function withSetup( callable $callback ) : self
	{
		return $this->withFilter( $callback( $this->filter ));
	}
}
