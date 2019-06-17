<?php

namespace Kucbel\Scalar\Input;

use Nette\Http\IRequest;
use Nette\InvalidArgumentException;

class RequestInput extends Input
{
	const
		POST	= 1,
		QUERY	= 2,
		HEADER	= 3;

	/**
	 * @var IRequest
	 */
	private $request;

	/**
	 * @var int
	 */
	private $source;

	/**
	 * RequestInput constructor.
	 *
	 * @param IRequest $request
	 * @param int $source
	 */
	function __construct( IRequest $request, int $source = null )
	{
		if( $source === null ) {
			$source = $request->isMethod( IRequest::POST ) ? self::POST : self::QUERY;
		}

		if( $source !== self::POST and $source !== self::QUERY and $source !== self::HEADER ) {
			throw new InvalidArgumentException("Invalid source option.");
		}

		$this->request = $request;
		$this->source = $source;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		switch( $this->source ) {
			case self::POST:
				return $this->request->getPost( $name );
			case self::QUERY:
				return $this->request->getQuery( $name );
			case self::HEADER:
				return $this->request->getHeader( $name );
			default:
				return null;
		}
	}
}
