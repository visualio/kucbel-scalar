<?php

namespace Kucbel\Scalar\Input;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Nette\InvalidArgumentException;

class DocumentInput extends Input
{
	/**
	 * @var DOMDocument
	 */
	private $document;

	/**
	 * @var DOMElement
	 */
	private $element;

	/**
	 * @var DOMXPath
	 */
	private $search;

	/**
	 * DocumentInput constructor.
	 *
	 * @param DOMDocument $document
	 * @param DOMElement $element
	 * @param DOMXPath $search
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
	function get( string $name )
	{
		$query = @$this->search->query( $name, $this->element );

		if( $query === false ) {
			throw new InputException("Query '$name' is malformed.");
		}

		if( $query->length === 1 ) {
			return $query->item(0)->nodeValue;
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
}