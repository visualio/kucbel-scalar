<?php

namespace Kucbel\Scalar\Input;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMXPath;
use Nette\FileNotFoundException;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

class DocumentInput extends Input implements DetectInterface
{
	/**
	 * @var DOMDocument
	 */
	protected $document;

	/**
	 * @var DOMElement
	 */
	protected $element;

	/**
	 * @var DOMXPath
	 */
	protected $search;

	/**
	 * DocumentInput constructor.
	 *
	 * @param DOMDocument $document
	 * @param DOMElement | null $element
	 * @param DOMXPath | null $search
	 */
	function __construct( DOMDocument $document, DOMElement $element = null, DOMXPath $search = null )
	{
		if( $document->firstChild === null ) {
			throw new InvalidArgumentException("Document isn't loaded.");
		} elseif( $element and $element->ownerDocument !== $document ) {
			throw new InvalidArgumentException("Element isn't related.");
		} elseif( $search and $search->document !== $document ) {
			throw new InvalidArgumentException("Search isn't related.");
		}

		if( $search === null ) {
			$search = new DOMXPath( $document );

			foreach( $search->query('namespace::*') as $node ) {
				$search->registerNamespace( $node->localName, $node->nodeValue );
			}
		}

		$this->document = $document;
		$this->element = $element ?? $document;
		$this->search = $search;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function get( string $name ) : mixed
	{
		$query = @$this->search->query( $name, $this->element );

		if( $query === false ) {
			throw new InvalidArgumentException("Query '$name' is malformed.");
		}

		if( $query->length === 1 ) {
			return $query->item( 0 )->nodeValue;
		} elseif( $query->length > 1 ) {
			$list = null;

			foreach( $query as $each ) {
				$list[] = $each->nodeValue;
			}

			return $list;
		} else {
			return null;
		}
	}

	/**
	 * @param string $file
	 * @return DocumentInput
	 */
	static function file( string $file ) : self
	{
		libxml_clear_errors();

		$document = new DOMDocument;

		if( !@$document->load( $file )) {
			if( $error = libxml_get_last_error() ) {
				$message = Strings::replace( $error->message, '~line [0-9]+.*$~D');
				$message = Strings::trim( $message );

				$previous = new DOMException( "{$message}, line {$error->line}.", $error->code );
			} else {
				$previous = null;
			}

			throw new FileNotFoundException("File '$file' isn't readable.", null, $previous );
		}

		return new self( $document );
	}

	/**
	 * @param mixed $source
	 * @return bool
	 */
	static function handle( mixed $source ) : bool
	{
		return $source instanceof DOMDocument;
	}
}
