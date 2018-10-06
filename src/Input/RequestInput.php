<?php

namespace Kucbel\Scalar\Input;

use Nette\Http\IRequest;

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
		$this->request = $request;
		$this->source = $source ?? ( $request->isMethod( IRequest::POST ) ? self::POST : self::QUERY );
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name )
	{
		if( $this->source === self::POST ) {
			return $this->request->getPost( $name );
		} elseif( $this->source === self::QUERY ) {
			return $this->request->getQuery( $name );
		} elseif( $this->source === self::HEADER ) {
			return $this->request->getHeader( $name );
		} else {
			throw new InputException("Source #{$this->source} doesn't exist.");
		}
	}
}