<?php

namespace Kucbel\Scalar\Input;

use DOMDocument;
use DOMElement;
use DOMXPath;

class DocumentInput extends StrictInput
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
	 * XmlInput constructor.
	 *
	 * @param DOMDocument $document
	 * @param DOMElement $element
	 */
	function __construct( DOMDocument $document, DOMElement $element = null )
	{
		if( $document->firstChild === null ) {
			throw new InputException("Document isn't loaded.");
		} elseif( $element and $element->ownerDocument !== $document ) {
			throw new InputException("Element isn't attached.");
		}

		$search = new DOMXPath( $document );

		foreach( $search->query('namespace::*') as $node ) {
			$search->registerNamespace( $node->localName, $node->nodeValue );
		}

		$this->document = $document;
		$this->element = $element ?? $document;
		$this->search = $search;
	}

	/**
	 * @param string $name
	 * @param mixed $null
	 * @return mixed
	 */
	function get( string $name, $null = null )
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
			return $null;
		}
	}
}