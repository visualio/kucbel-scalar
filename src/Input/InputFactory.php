<?php

namespace Kucbel\Scalar\Input;

use Kucbel\Scalar\Filter\FilterFactory;
use Kucbel\Scalar\Validator\MixedValidator;
use Nette\SmartObject;

class InputFactory
{
	use SmartObject;

	const
		INPUT	= 0b1,
		VALUE	= 0b10,
		POOL	= 0b100,
		LIST	= 0b1000;

	/**
	 * @var FilterFactory
	 */
	protected $factory;

	/**
	 * @var array
	 */
	protected $inputs = [
		ComponentInput::class,
		RequestInput::class,
		ConsoleInput::class,
		DocumentInput::class,
		ArrayInput::class,
		MixedInput::class,
	];

	/**
	 * @var array
	 */
	protected $filters = [
		self::INPUT		=> InputFilter::WRAP,
		self::VALUE		=> InputFilter::WRAP,
		self::POOL 		=> InputFilter::WRAP,
		self::LIST 		=> InputFilter::WRAP,
	];

	/**
	 * @var array
	 */
	protected $adapters = [
		self::POOL		=> InputAdapter::QUERY,
		self::LIST		=> InputAdapter::QUERY | InputAdapter::MERGE,
	];

	/**
	 * InputFactory constructor.
	 *
	 * @param FilterFactory $factory
	 * @param array $inputs
	 * @param array $filters
	 * @param array $adapters
	 */
	function __construct( FilterFactory $factory, array $inputs = null, array $filters = null, array $adapters = null )
	{
		$this->factory = $factory;

		if( $inputs ) {
			$this->inputs = $inputs;
		}

		if( $filters ) {
			$this->filters = $filters + $this->filters;
		}

		if( $adapters ) {
			$this->adapters = $adapters + $this->adapters;
		}
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

		$type = is_object( $source ) ? get_class( $source ) : gettype( $source );

		throw new InputException("No input registered for '$type'.");
	}

	/**
	 * @param mixed $source
	 * @param mixed ...$options
	 * @return InputInterface
	 */
	function create( $source, ...$options ) : InputInterface
	{
		$input = $this->wrap( $source, ...$options );

		if( $this->filters[ self::INPUT ] & InputFilter::WRAP ) {
			$input = $this->factory->create( $input );
		}

		return $input;
	}

	/**
	 * @param mixed ...$sources
	 * @return InputInterface
	 */
	function pool( ...$sources ) : InputInterface
	{
		if( !$sources ) {
			throw new InputException("No source provided.");
		}

		$index = 0;

		foreach( $sources as $index => $source ) {
			if( is_array( $source ) and array_key_exists( 0, $source )) {
				$input = $this->wrap( ...$source );
			} else {
				$input = $this->wrap( $source );
			}

			if( $this->filters[ self::POOL ] & InputFilter::EACH ) {
				$input = $this->factory->create( $input );
			}

			$sources[ $index ] = $input;
		}

		if( $index > 0 ) {
			$input = new InputPool( ...$sources );
			$input->mode( $this->adapters[ self::POOL ] );

			if( $this->filters[ self::POOL ] & InputFilter::WRAP ) {
				$input = $this->factory->create( $input );
			}
		} else {
			$input = $sources[0];

			if( $this->filters[ self::POOL ] & InputFilter::WRAP and !( $this->filters[ self::POOL ] & InputFilter::EACH )) {
				$input = $this->factory->create( $input );
			}
		}

		return $input;
	}

	/**
	 * @param mixed ...$sources
	 * @return InputInterface
	 */
	function list( ...$sources ) : InputInterface
	{
		if( !$sources ) {
			throw new InputException("No source provided.");
		}

		foreach( $sources as $index => $source ) {
			if( is_array( $source ) and array_key_exists( 0,$source )) {
				$input = $this->wrap( ...$source );
			} else {
				$input = $this->wrap( $source );
			}

			if( $this->filters[ self::LIST ] & InputFilter::EACH ) {
				$input = $this->factory->create( $input );
			}

			$sources[ $index ] = $input;
		}

		$input = new InputList( ...$sources );
		$input->mode( $this->adapters[ self::LIST ] );

		if( $this->filters[ self::LIST ] & InputFilter::WRAP ) {
			$input = $this->factory->create( $input );
		}

		return $input;
	}

	/**
	 * @param mixed $value
	 * @param string $name
	 * @return MixedValidator
	 */
	function value( $value, string $name = '???') : MixedValidator
	{
		$mixed = new MixedValidator( $name, $value );

		if( $this->filters[ self::VALUE ] & InputFilter::WRAP ) {
			$mixed->clear( $this->factory->get() );
		}

		return $mixed;
	}

	/**
	 * @return InputBuilder
	 */
	function setup() : InputBuilder
	{
		return new InputBuilder( $this->factory->setup(), $this->inputs, $this->filters, $this->adapters );
	}
}
