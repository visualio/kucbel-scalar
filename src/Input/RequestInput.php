<?php

namespace Kucbel\Scalar\Input;

use Nette\Http\IRequest;
use Nette\InvalidArgumentException;

class RequestInput extends Input implements DetectInterface
{
	const
		POST	= 1,
		QUERY	= 2,
		HEADER	= 3;

	/**
	 * @var IRequest
	 */
	protected $request;

	/**
	 * @var int
	 */
	protected $source;

	/**
	 * RequestInput constructor.
	 *
	 * @param IRequest $request
	 * @param int | null $source
	 */
	function __construct( IRequest $request, int $source = null )
	{
		if( $source === null ) {
			$source = $request->isMethod( IRequest::POST ) ? self::POST : self::QUERY;
		}

		if( $source !== self::POST and $source !== self::QUERY and $source !== self::HEADER ) {
			throw new InvalidArgumentException("Unknown source.");
		}

		$this->request = $request;
		$this->source = $source;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name ) : mixed
	{
		if( $this->source === self::POST ) {
			return $this->request->getPost( $name );
		} elseif( $this->source === self::QUERY ) {
			return $this->request->getQuery( $name );
		} else {
			return $this->request->getHeader( $name );
		}
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( mixed $source ) : bool
	{
		return $source instanceof IRequest;
	}
}
