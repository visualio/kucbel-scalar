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
		POOL	= 0b10,
		LIST	= 0b100;

	/**
	 * @var FilterFactory
	 */
	protected $factory;

	/**
	 * @var array
	 */
	protected $inputs = [
		ComponentInput::class	=> 'Nette\Application\UI\Component',
		RequestInput::class		=> 'Nette\Http\IRequest',
		ConsoleInput::class		=> 'Symfony\Component\Console\Input\InputInterface',
		JsonInput::class		=> 'stdClass',
		ArrayInput::class		=> 'ArrayAccess',
		DocumentInput::class	=> 'DOMDocument',
	];

	/**
	 * @var array
	 */
	protected $filters = [
		self::INPUT		=> InputFilter::WRAP,
		self::POOL 		=> InputFilter::WRAP,
		self::LIST 		=> InputFilter::WRAP,
	];

	/**
	 * @var array
	 */
	protected $adapters = [
		self::POOL		=> InputAdapter::QUERY,
		self::LIST		=> InputAdapter::QUERY,
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
			$this->inputs = $inputs + $this->inputs;
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
		foreach( $this->inputs as $input => $check ) {
			if( $source instanceof $check ) {
				return new $input( $source, ...$options );
			}
		}

		if( $source instanceof InputInterface ) {
			return $source;
		}

		$type = is_object( $source ) ? get_class( $source ) : gettype( $source );

		throw new InputException("Type '$type' doesn't have input assigned.");
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
	 * @param mixed $source
	 * @param mixed ...$sources
	 * @return InputInterface
	 */
	function pool( $source, ...$sources ) : InputInterface
	{
		array_unshift( $sources, $source );

		$i = 0;

		foreach( $sources as $i => $source ) {
			$input = is_array( $source ) ? $this->wrap( ...$source ) : $this->wrap( $source );

			if( $this->filters[ self::POOL ] & InputFilter::EACH ) {
				$input = $this->factory->create( $input );
			}

			$sources[ $i ] = $input;
		}

		if( $i > 0 ) {
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
	 * @param mixed $source
	 * @param mixed ...$sources
	 * @return InputInterface
	 */
	function list( $source, ...$sources ) : InputInterface
	{
		array_unshift( $sources, $source );

		foreach( $sources as $i => $source ) {
			$input = is_array( $source ) ? $this->wrap( ...$source ) : $this->wrap( $source );

			if( $this->filters[ self::LIST ] & InputFilter::EACH ) {
				$input = $this->factory->create( $input );
			}

			$sources[ $i ] = $input;
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

		if( $this->filters[ self::INPUT ] & InputFilter::WRAP ) {
			$mixed->clear( $this->factory->get() );
		}

		return $mixed;
	}

	/**
	 * @return InputBuilder
	 */
	function build() : InputBuilder
	{
		return new InputBuilder( $this->factory->build(), $this->inputs, $this->filters, $this->adapters );
	}
}