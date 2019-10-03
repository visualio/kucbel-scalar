<?php

namespace Kucbel\Scalar\DI;

use AppendIterator;
use ArrayIterator;
use Kucbel\Scalar\Filter;
use Kucbel\Scalar\Input;
use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Iterator;
use Kucbel\Scalar\Schema;
use Kucbel\Scalar\Validator;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;
use Nette\Neon\Exception as NeonException;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\JsonException;
use ReflectionClass;

class ScalarExtension extends CompilerExtension
{
	/**
	 * @var Input\InputReflector
	 */
	private $reflector;

	/**
	 * @var Schema\TypeGenerator
	 */
	private $generator;

	/**
	 * @var array
	 */
	private $methods;

	/**
	 * @var array | null
	 */
	private $inputs;

	/**
	 * @var array | null
	 */
	private $filters = [
		Filter\TrimFilter::class,
		Filter\RoundFilter::class,
	];

	/**
	 * @var array | null
	 */
	private $schemas;

	/**
	 * @var array | null
	 */
	private $types = [
		'bool'			=> [['bool']],
		'bool|null'		=> [['optional'], ['@bool']],
		'int'			=> [['integer']],
		'int|null'		=> [['optional'], ['@int']],
		'float'			=> [['float']],
		'float|null'	=> [['optional'], ['@float']],
		'str'			=> [['string']],
		'str|null'		=> [['optional'], ['@str']],
		'str255'		=> [['@str'], ['char', 1, 255 ]],
		'str255|null'	=> [['optional'], ['@str255']],
		'date'			=> [['date']],
		'date|null'		=> [['optional'], ['@date']],
		'date|now'		=> [['optional', 'now'], ['@date']],
		'id'			=> [['integer'], ['value', 1, null ]],
		'id|null'		=> [['optional'], ['@id']],
		'ids'			=> [['array'], ['count', 1, null ], ['@id']],
		'ids|null'		=> [['optional'], ['@ids']],
	];

	/**
	 * @var array
	 */
	private $argues = [
		'types'			=> null,
		'filters'		=> null,
	];

	/**
	 * InputExtension constructor.
	 */
	function __construct()
	{
		$deflect = function( $class ) {
			return new ReflectionClass( $class );
		};

		$imports = array_map( $deflect, [
			Validator\BoolValidator::class,		Validator\DateValidator::class,		Validator\FloatValidator::class,
			Validator\IntegerValidator::class,	Validator\MixedValidator::class,	Validator\StringValidator::class,
			Iterator\BoolIterator::class,		Iterator\DateIterator::class,		Iterator\FloatIterator::class,
			Iterator\IntegerIterator::class,	Iterator\MixedIterator::class,		Iterator\StringIterator::class,
		]);

		$ignores = array_map( $deflect, [
			Filter\FilterInterface::class,
			Iterator\IteratorInterface::class,
			Validator\ValidatorInterface::class,
		]);

		$this->reflector = new Input\InputReflector( ...$ignores );
		$this->generator = new Schema\TypeGenerator;

		$this->methods = $this->reflector->getMethods( ...$imports );
	}

	/**
	 * Compose
	 *
	 * @throws NeonException
	 * @throws JsonException
	 */
	function loadConfiguration() : void
	{
		$config = $this->getParameters();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition( $this->prefix('filter.trim'))
			->setType( Filter\TrimFilter::class )
			->setArguments([ $config['trim'] ]);

		$builder->addDefinition( $this->prefix('filter.round'))
			->setType( Filter\RoundFilter::class )
			->setArguments([ $config['round'] ]);

		$builder->addDefinition( $filter = $this->prefix('filter.factory'))
			->setType( Filter\FilterFactory::class )
			->setArguments([ &$this->argues['filters'] ]);

		$builder->addDefinition( $this->prefix('input.factory'))
			->setType( Input\InputFactory::class )
			->setArguments(["@$filter", &$this->inputs, null, null ]);

		$builder->addDefinition( $type = $this->prefix('type.factory'))
			->setType( Schema\TypeFactory::class )
			->setArguments([ &$this->argues['types'] ]);

		$builder->addDefinition( $this->prefix('schema.factory'))
			->setType( Schema\SchemaFactory::class )
			->setArguments(["@$type", &$this->schemas ]);

		$builder->addDefinition( $this->prefix('assert.factory'))
			->setType( Schema\AssertFactory::class )
			->setArguments(["@$type"]);

		$input = new Input\ExtensionInput( $this );

		$this->setInputs( $input );
		$this->setFilters( $input );
		$this->setSchemas( $input );
		$this->setTypes( $input );
		$this->addFiles( $input );

		$input->match();
	}

	/**
	 * Compile
	 */
	function beforeCompile() : void
	{
		foreach( $this->types ?? [] as $i => $rules ) {
			$rules = $this->getResolvedType($i, $rules );

			$this->argues['types'][ $i ] = $this->generator->compress( ...$rules );
		}

		foreach( $this->filters as $i => $filter ) {
			$this->argues['filters'][ $i ] = "@{$filter}";
		}
	}

	/**
	 * @param ClassType $class
	 */
	function afterCompile( ClassType $class )
	{
		if( array_diff_key( $this->types, $this->argues['types'] ) or array_diff_key( $this->filters, $this->argues['filters'] )) {
			throw new InvalidStateException("Configuration was modified after extension compilation.");
		}
	}

	/**
	 * @return array
	 */
	protected function getParameters() : array
	{
		$modes = [
			Filter\TrimFilter::STRING	=> 'string',
			Filter\TrimFilter::ARRAY	=> 'array',
			Filter\TrimFilter::EMPTY	=> 'empty',
			Filter\TrimFilter::SPACE	=> 'space',
			Filter\TrimFilter::BREAK	=> 'break',
		];

		$input = new Input\ExtensionInput( $this );

		$array = $input->create('trim')
			->optional(['string', 'array', 'empty'])
			->array()
			->string()
			->unique()
			->equal( ...$modes );

		$flags = $array->fetch();

		if( in_array('break', $flags, true )) {
			$array->exist('string', 'space');
		} elseif( in_array('space', $flags, true )) {
			$array->exist('string');
		}

		$param['trim'] = 0;

		foreach( $flags as $flag ) {
			$param['trim'] |= array_search( $flag, $modes );
		}

		$param['round'] = $input->create('round')
			->optional( 14 )
			->integer()
			->value( 1, 99 )
			->fetch();

		return $param;
	}

	/**
	 * @param InputInterface $input
	 */
	function setTypes( InputInterface $input ) : void
	{
		if( $input->has('types')) {
			$this->types = null;

			$this->addTypes( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addTypes( InputInterface $input ) : void
	{
		$types = $input->create('types')
			->optional()
			->index()
			->string()
			->match('~^[^.]+$~')
			->match('~[a-z]~')
			->fetch();

		foreach( $types ?? [] as $type ) {
			$this->addType( $input, $type );
		}
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string $alias
	 */
	function addType( InputInterface $input, string $name, string $alias = null ) : void
	{
		$rules = [];
		$tests = $input->create("types.$name")
			->index()
			->count( 1, null )
			->integer()
			->fetch();

		foreach( $tests as $test ) {
			$array = $input->create("types.$name.$test")
				->array();

			$first = $array->first()
				->string();

			try {
				$array->count( 1, 1 );
				$first->match('~^@.~');
			} catch( Validator\ValidatorException $ex ) {
				$first->equal( ...$this->methods );
			}

			$rules[] = $array->fetch();
		}

		if( $this->hasObject( $rules )) {
			throw new InvalidStateException("Type '{$name}' contains DateTime object.");
		}

		$this->types[ $alias ?? $name ] = $rules;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function hasType( string $name ) : bool
	{
		return isset( $this->types[ $name ] );
	}

	/**
	 * @param array $rules
	 * @return bool
	 */
	protected function hasObject( array $rules ) : bool
	{
		$queue = new AppendIterator;
		$queue->append( new ArrayIterator( $rules ));

		foreach( $queue as $item ) {
			if( is_array( $item )) {
				$queue->append( new ArrayIterator( $item ));
			} elseif( is_object( $item )) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $name
	 * @param array $rules
	 * @return array
	 */
	protected function getResolvedType( string $name, array $rules ) : array
	{
		$links = [ $name ];

		for( $index = 0; isset( $rules[ $index ] ); $index++ ) {
			$link = $rules[ $index ][0];

			if( $link[0] !== '@') {
				continue;
			}

			$link = ltrim( $link, '@');

			if( in_array( $link, $links, true )) {
				unset( $links[0] );

				$loop = implode("', '", $links );

				throw new InvalidStateException("Type '$name' contains reference loop '$loop', '$link'.");
			}

			$type = $this->types[ $link ] ?? null;

			if( !$type ) {
				throw new InvalidStateException("Type '$name' contains invalid reference '$link'.");
			}

			$rules = array_merge(
				array_slice( $rules, 0, $index ),
				$type,
				array_slice( $rules, $index + 1 ));

			$links[] = $link;
			$index--;
		}

		return $rules;
	}

	/**
	 * @param InputInterface $input
	 */
	function setSchemas( InputInterface $input ) : void
	{
		if( $input->has('schemas')) {
			$this->schemas = null;

			$this->addSchemas( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addSchemas( InputInterface $input ) : void
	{
		$schemas = $input->create('schemas')
			->optional()
			->index()
			->string()
			->match('~^[^.]+$~')
			->match('~[a-z]~')
			->fetch();

		foreach( $schemas ?? [] as $schema ) {
			$this->addSchema( $input, $schema );
		}
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string $alias
	 */
	function addSchema( InputInterface $input, string $name, string $alias = null ) : void
	{
		$types = $input->create("schemas.$name")
			->optional()
			->index()
			->string()
			->match('~^[^.]+$~')
			->match('~[a-z]~')
			->fetch();

		$schema = [];

		foreach( $types ?? [] as $type ) {
			$what = $input->get("schemas.$name.$type");

			if( is_array( $what )) {
				$schema[ $type ] = $this->getAnonymousType( $input, $name, $type );
			} else {
				$schema[ $type ] = $this->getExistingType( $input, $name, $type );
			}
		}

		$this->schemas[ $alias ?? $name ] = $schema;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function hasSchema( string $name ) : bool
	{
		return isset( $this->schemas[ $name ] );
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	protected function getExistingType( InputInterface $input, string $name, string $type ) : string
	{
		return $input->create("schemas.$name.$type")
			->string()
			->match('~^[^.]+$~')
			->match('~^[^@]+~')
			->fetch();
	}

	/**
	 * @param InputInterface $input
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	protected function getAnonymousType( InputInterface $input, string $name, string $type ) : string
	{
		do {
			$alias = mt_rand( 1000, 9999 );
			$alias = "{$type}_{$alias}";
		} while( $this->hasType( $alias ));

		$rules = $input->get("schemas.$name.$type");

		$input = new Input\MixedInput(['types' => [ $type => $rules ]], $this->name );

		$this->addType( $input, $type, $alias );

		return $alias;
	}

	/**
	 * @param InputInterface $input
	 */
	function setInputs( InputInterface $input ) : void
	{
		if( $input->has('inputs')) {
			$this->inputs = null;

			$this->addInputs( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addInputs( InputInterface $input ) : void
	{
		$classes = $input->create('inputs')
			->optional()
			->array()
			->string()
			->class( Input\DetectInterface::class )
			->unique()
			->fetch();

		foreach( $classes ?? [] as $class ) {
			if( !in_array( $class, $this->inputs ?? [], true )) {
				$this->inputs[] = $class;
			}
		}
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	function hasInput( string $class ) : bool
	{
		return in_array( $class, $this->inputs ?? [], true );
	}

	/**
	 * @param InputInterface $input
	 * @throws NeonException
	 * @throws JsonException
	 */
	function addFiles( InputInterface $input ) : void
	{
		$files = $input->create('files')
			->optional()
			->array()
			->string()
			->match('~[.](neon|json)$~')
			->file()
			->unique()
			->fetch();

		foreach( $files ?? [] as $file ) {
			$input = Input\MixedInput::file( $file, $this->name );

			$this->addTypes( $input );
			$this->addSchemas( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function setFilters( InputInterface $input ) : void
	{
		if( $input->has('filters')) {
			$this->filters = null;

			$this->addFilters( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addFilters( InputInterface $input ) : void
	{
		$filters = $input->create('filters')
			->optional()
			->array()
			->string()
			->class( Filter\FilterInterface::class )
			->unique()
			->fetch();

		foreach( $filters ?? [] as $filter ) {
			$this->filters[] = $filter;
		}
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	function hasFilter( string $class ) : bool
	{
		return in_array( $class, $this->filters ?? [], true );
	}

	/**
	 * @param CompilerExtension $extension
	 * @param array $files
	 * @throws NeonException
	 * @throws JsonException
	 */
	static function addExtension( CompilerExtension $extension, array $files ) : void
	{
		if( !$extension->compiler ) {
			throw new InvalidStateException("Extension isn't attached.");
		}

		$scalar = current( $extension->compiler->getExtensions( self::class ));

		if( !$scalar instanceof self ) {
			throw new InvalidStateException("Extension isn't installed.");
		}

		$input = new Input\MixedInput(['files' => $files ], $extension->name );

		$scalar->addFiles( $input );
	}
}
