<?php

namespace Kucbel\Scalar\DI;

use Kucbel\Scalar\Filter;
use Kucbel\Scalar\Input;
use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Iterator;
use Kucbel\Scalar\Schema;
use Kucbel\Scalar\Schema\SchemaException;
use Kucbel\Scalar\Validator;
use Nette\DI\CompilerExtension;
use Nette\Neon\Neon;
use Nette\PhpGenerator\PhpLiteral;
use ReflectionClass;
use ReflectionException;

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
	private $filters;

	/**
	 * @var array | null
	 */
	private $aliases;

	/**
	 * @var array | null
	 */
	private $schemas;

	/**
	 * @var array | null
	 */
	private $types = [
		'bool'			=> ['bool'],
		'bool|null'		=> ['optional', '@bool'],
		'int'			=> ['integer'],
		'int|null'		=> ['optional', '@int'],
		'float'			=> ['float'],
		'float|null'	=> ['optional', '@float'],
		'str'			=> ['string'],
		'str|null'		=> ['optional', '@str'],
		'str255'		=> ['@str', ['char', 255 ]],
		'str255|null'	=> ['optional' ,'@str255'],
		'date'			=> ['date'],
		'date|null'		=> ['optional', '@date'],
		'date|now'		=> [['optional', 'now'], '@date'],
		'id'			=> ['integer', ['value', 1, null ]],
		'id|null'		=> ['optional', '@id'],
		'ids'			=> ['array', ['count', 1, null ], '@id'],
		'ids|null'		=> ['optional', '@ids'],
	];

	/**
	 * InputExtension constructor.
	 */
	function __construct()
	{
		$deflect = function( $class ) {
			return new ReflectionClass( $class );
		};

		$classes = array_map( $deflect, [
			Validator\BoolValidator::class,		Validator\DateValidator::class,		Validator\FloatValidator::class,
			Validator\IntegerValidator::class,	Validator\MixedValidator::class,	Validator\StringValidator::class,
			Iterator\BoolIterator::class,		Iterator\DateIterator::class,		Iterator\FloatIterator::class,
			Iterator\IntegerIterator::class,	Iterator\MixedIterator::class,		Iterator\StringIterator::class,
		]);

		$ignore = array_map( $deflect, [
			Filter\FilterInterface::class,
			Iterator\IteratorInterface::class,
			Validator\ValidatorInterface::class,
		]);

		$this->reflector = new Input\InputReflector( ...$ignore );
		$this->generator = new Schema\TypeGenerator;

		$this->methods = $this->reflector->getMethods( ...$classes );
	}

	/**
	 * Compose
	 *
	 * @throws ReflectionException
	 */
	function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition( $trim = $this->prefix('filter.trim'))
			->setType( Filter\TrimFilter::class );

		$builder->addDefinition( $round = $this->prefix('filter.round'))
			->setType( Filter\RoundFilter::class )
			->setArguments([ 14 ]);

		$builder->addDefinition( $this->prefix('filter.factory'))
			->setType( Filter\FilterFactory::class );

		$builder->addDefinition( $this->prefix('input.factory'))
			->setType( Input\InputFactory::class );

		$builder->addDefinition( $this->prefix('type.factory'))
			->setType( Schema\TypeFactory::class );

		$builder->addDefinition( $this->prefix('schema.factory'))
			->setType( Schema\SchemaFactory::class );

		$this->filters[] = $this->aliases['@trim'] = "@$trim";
		$this->filters[] = $this->aliases['@round'] = "@$round";

		$input = new Input\DirectInput(['types' => $this->types ], $this->name );

		$this->setTypes( $input );

		$input = new Input\ExtensionInput( $this );

		$this->setFilters( $input );
		$this->setSchemas( $input );
		$this->setTypes( $input );
		$this->addFiles( $input );
		$this->addInputs( $input );

		$input->match();
	}

	/**
	 * Compile
	 */
	function beforeCompile()
	{
		$tests = null;

		foreach( $this->types ?? [] as $name => $rules ) {
			$rules = $this->getResolvedType($name, $rules );

			$tests[ $name ] = $this->generator->compress( ...$rules );
		}

		$builder = $this->getContainerBuilder();

		$builder->getDefinition( $filter = $this->prefix('filter.factory'))
			->setArguments( $this->filters ?? [ new PhpLiteral('') ]);

		$builder->getDefinition( $this->prefix('input.factory'))
			->setArguments(["@$filter", $this->inputs, null, null ]);

		$builder->getDefinition( $type = $this->prefix('type.factory'))
			->setArguments([ $tests ]);

		$builder->getDefinition( $this->prefix('schema.factory'))
			->setArguments(["@$type", $this->schemas ]);
	}

	/**
	 * @param InputInterface $input
	 */
	function setTypes( InputInterface $input )
	{
		if( $input->has('types')) {
			$this->types = null;

			$this->addTypes( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addTypes( InputInterface $input )
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
	function addType( InputInterface $input, string $name, string $alias = null )
	{
		$rules = [];
		$tests = $input->create("types.$name")
			->index()
			->count( 1, null )
			->integer()
			->fetch();

		foreach( $tests as $test ) {
			$mixed = $input->create("types.$name.$test");
			$value = $mixed->fetch();

			if( is_string( $value ) and !strncmp( $value, '@', 1 )) {
				$rules[] = [ $value ];
			} else {
				$array = $mixed->array();

				$array->first()
					->string()
					->equal( ...$this->methods );

				$rules[] = $array->fetch();
			}
		}

		$this->types[ $alias ?? $name ] = $rules;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function hasType( string $name )
	{
		return isset( $this->types[ $name ] );
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
			$link = current( $rules[ $index ] );

			if( strncmp( $link, '@', 1 )) {
				continue;
			}

			$link = ltrim( $link, '@');

			if( in_array( $link, $links, true )) {
				unset( $links[0] );

				$loop = implode("', '", $links );

				throw new SchemaException("Type '$name' contains reference loop '$loop', '$link'.");
			}

			$type = $this->types[ $link ] ?? null;

			if( !$type ) {
				throw new SchemaException("Type '$name' contains invalid reference '$link");
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
	function setSchemas( InputInterface $input )
	{
		if( $input->has('schemas')) {
			$this->schemas = null;

			$this->addSchemas( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addSchemas( InputInterface $input )
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
	function addSchema( InputInterface $input, string $name, string $alias = null )
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

		$input = new Input\DirectInput(['types' => [ $type => $rules ]], $this->name );

		$this->addType( $input, $type, $alias );

		return $alias;
	}

	/**
	 * @param InputInterface $input
	 * @throws ReflectionException
	 */
	function setInputs( InputInterface $input )
	{
		if( $input->has('inputs')) {
			$this->inputs = null;

			$this->addInputs( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 * @throws ReflectionException
	 */
	function addInputs( InputInterface $input )
	{
		$classes = $input->create('inputs')
			->optional()
			->array()
			->string()
			->impl( InputInterface::class )
			->unique()
			->fetch();

		foreach( $classes ?? [] as $class ) {
			$this->inputs[ $class ] = $this->reflector->getArgument( new ReflectionClass( $class ));
		}
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	function hasInput( string $class ) : bool
	{
		return isset( $this->inputs[ $class ] );
	}

	/**
	 * @param InputInterface $input
	 */
	function addFiles( InputInterface $input )
	{
		$files = $input->create('files')
			->optional()
			->array()
			->string()
			->match('~[.]neon$~')
			->file( true )
			->unique()
			->fetch();

		foreach( $files ?? [] as $file ) {
			$data = Neon::decode( file_get_contents( $file ));

			if( !is_array( $data )) {
				throw new SchemaException("File '$file' doesn't have correct format.");
			}

			$input = new Input\DirectInput( $data, $this->name );

			$this->addTypes( $input );
			$this->addSchemas( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function setFilters( InputInterface $input )
	{
		if( $input->has('filters')) {
			$this->filters = null;

			$this->addFilters( $input );
		}
	}

	/**
	 * @param InputInterface $input
	 */
	function addFilters( InputInterface $input )
	{
		$filters = $input->create('filters')
			->optional()
			->array()
			->string()
			->match('~^@[^@]+.~')
			->unique()
			->fetch();

		foreach( $filters ?? [] as $filter ) {
			$this->filters[] = $this->aliases[ $filter ] ?? $filter;
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function hasFilter( string $name ) : bool
	{
		if(( $name[0] ?? null ) !== '@') {
			$name = "@{$name}";
		}

		return in_array( $this->aliases[ $name ] ?? $name, $this->filters ?? [], true );
	}

	/**
	 * @param CompilerExtension $extension
	 * @param array $files
	 */
	static function addExtension( CompilerExtension $extension, array $files )
	{
		if( !$extension->compiler ) {
			throw new SchemaException("Extension isn't loaded.");
		}

		$scalar = current( $extension->compiler->getExtensions( self::class ));

		if( !$scalar instanceof self ) {
			throw new SchemaException("Extension isn't installed.");
		}

		$input = new Input\DirectInput(['files' => $files ], $extension->name );

		$scalar->addFiles( $input );
	}
}
