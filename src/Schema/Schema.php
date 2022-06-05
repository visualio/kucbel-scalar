<?php

namespace Kucbel\Scalar\Schema;

use Kucbel\Scalar\Input\InputInterface;
use Kucbel\Scalar\Input\VoidInput;
use Kucbel\Scalar\Output\ArrayOutput;
use Kucbel\Scalar\Output\CompareOutput;
use Kucbel\Scalar\Output\OutputInterface;
use Kucbel\Scalar\Schema\Type\TypeFactory;
use Kucbel\Scalar\Validator\ValidatorException;
use Nette\InvalidArgumentException;
use Nette\SmartObject;

class Schema
{
	use SmartObject;

	/**
	 * @var TypeFactory
	 */
	protected $type;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var array
	 */
	protected $schema;

	/**
	 * @var bool
	 */
	public $entire = false;

	/**
	 * Schema constructor.
	 *
	 * @param TypeFactory $type
	 * @param InputInterface $input
	 * @param array $schema
	 */
	function __construct( TypeFactory $type, InputInterface $input, array $schema )
	{
		$this->type = $type;
		$this->input = $input;
		$this->schema = $schema;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function value( string $name ) : mixed
	{
		$type = $this->schema[ $name ] ?? null;

		if( $type === null ) {
			throw new InvalidArgumentException("Parameter '$name' doesn't exist.");
		}

		try {
			return $this->type->get( $type )->fetch( $this->input->create( $name ));
		} catch( ValidatorException $error ) {
			throw new SchemaException( $error->withType( $type ));
		}
	}

	/**
	 * @param OutputInterface $output
	 */
	function write( OutputInterface $output ) : void
	{
		$errors = [];

		foreach( $this->schema as $name => $type ) {
			try {
				$output->set( $name, $this->type->get( $type )->fetch( $this->input->create( $name )));
			} catch( ValidatorException $error ) {
				$errors[] = $error->withType( $type );

				if( !$this->entire ) {
					break;
				}
			}
		}

		if( $errors ) {
			throw new SchemaException( ...$errors );
		}
	}

	/**
	 * @param InputInterface $input
	 * @return array | null
	 */
	function diff( InputInterface $input ) : ?iterable
	{
		$this->write( new CompareOutput( $input, $output = new ArrayOutput ));

		return $output->fetch();
	}

	/**
	 * @param bool $all
	 * @return array | null
	 */
	function fetch( bool $all = false ) : ?iterable
	{
		if( $all ) {
			$this->write( $output = new ArrayOutput );
		} else {
			$this->write( new CompareOutput( new VoidInput, $output = new ArrayOutput ));
		}

		return $output->fetch();
	}
}
