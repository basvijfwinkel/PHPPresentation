<?php

/**
 * This file is part of PHPPresentation - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPresentation is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPresentation/contributors.
 *
 * @see        https://github.com/PHPOffice/PHPPresentation
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

declare(strict_types=1);

namespace PhpOffice\PhpPresentation\PowerPoint2019Objects\Debug;

use DomDocument;
use DomElement;
use PhpOffice\Common\XMLReader;
use PhpOffice\PhpPresentation\PowerPoint2019Objects\Debug\DebugBase;

/**
 * ParseHelper
 */
class ParseHelper extends DebugBase
{
    public $hasParsingErrors = false;
    public $debugOwnParsingErrors = [];
    public $debugUnknownAttributes = [];
    public $debugUnknownChildNodes = [];
    public $debugParseErrorChildNodes = [];

    public function getParsingErrors($classname = null):string
    {

        if (is_null($classname)) { $classname = get_class($this); }
        if ($this->hasParsingErrors)
        {
            $report = "The ".$classname." element or its children have a parsing error:\n";

            // some parsing error of top level elements
            if ($this->debugOwnParsingErrors != [])
            {
               foreach($this->debugOwnParsingErrors as $ownError)
               {
                   $report .= "Error parsing ".$classname." : ".$ownError."\n";
               }
            }

            // unknown attributes
            if ($this->debugUnknownAttributes != [])
            {
                foreach($this->debugUnknownAttributes as $unknownAttributeName)
                {
                    $report .= "Unknown ".$classname." attribute ".$unknownAttributeName."\n";
                }
            }

            // unknown child elements
            if ($this->debugUnknownChildNodes != [])
            {
                foreach($this->debugUnknownChildNodes as $unknownChildNode)
                {
                    $report .= "Unknown ".$classname." child element named '".$unknownChildNode."\n";
                }
            }

            // parsing error in child element
            if ($this->debugParseErrorChildNodes != [])
            {
                foreach($this->debugParseErrorChildNodes as $childParseError)
                {
                    $report .= $childParseError;
                }
            }

        }
        else
        {
            // no parse error
            $report = "The ViewProps element was parsed without any errors.";
        }

        return $report;
    }

    // get all namespaces used in the xml document
    public function extractNamespaces(string $xmlstring): array
    {
       // use simplexml because it can get all namespaces directly
       // TODO : only extract the namespaces for the element because there might be child defined namespaces
       $xmldocument = simplexml_load_string(ltrim($xmlstring));
       $namespaces = $xmldocument->getDocNamespaces(true);
       return $namespaces;
    }

    public function setOwnParsingError(string $error)
    {
        $this->debugOwnParsingErrors[] = $error;
        $this->hasParsingErrors = true;
    }

    public function reportUnknownAttributes(array $knownAttributes, array $presentAttributes): void
    {
        //see if any of the present attributes is not present in the list of known attributes.
        $namespacedKnownAttributes = array_column($knownAttributes,'namespacedName');
        foreach($presentAttributes as $attr)
        {
            if (!in_array($attr, $namespacedKnownAttributes))
            {
                // found some unknown attribute
                $this->debugUnknownAttributes[] = $attr;
                $this->hasParsingErrors = true;
            }
        }
    }

    public function reportUnknownChildNodes(array $knownChildNodes, array $presentChildNodes): void
    {
        //see if any of the present childnodes is not present in the list of known child nodes.
        $namespacedKnownChildNodes = array_column($knownChildNodes,'namespacedName');
        foreach($presentChildNodes as $childNode)
        {
            if (!in_array($childNode, $namespacedKnownChildNodes))
            {
                // found some unknown childNode
                $this->debugUnknownChildNodes[] = $childNode;
                $this->hasParsingErrors = true;
            }
        }
    }

    public function getElementFromDom(XMLReader $xmlReader, string $elementName): DOMElement
    {
        // try to get the element from the dom
        $pathZoom = '/'.$elementName;
        $oElement = $xmlReader->getElement($pathZoom);
        if ($oElement instanceof DOMElement)
        {
            // found
            return $oElement;
        }
        else
        {
            // not found
            return null;
        }
    }

    public function getAllAttributes(DOMElement $element, string $elementName): array
    {
        $attributes = [];
        if (is_null($element)) { return $attributes; }

        foreach ($element->attributes as $attr)
        {
            $attributes[((($attr->prefix!=''))?$attr->prefix.':':'').$attr->nodeName] = $attr->nodeValue;
            if (strpos($attr->nodeName,',')!== false) { throw new Exception("Remove prefix addition in ParserHelper : getAllAttributes "); }
        }
        return $attributes;
    }

    public function getAllChildNodes(DOMElement $element, string $elementName): array
    {
        $childNodes = [];
        if (is_null($element)) { return $childNodes; }

        foreach ($element->childNodes as $childNode)
        {
            $childNodes[$childNode->nodeName] = $childNode;
        }
        return $childNodes;
    }

    public function printDomElement(DomElement $element):string
    {
        return $element->C14N();
    }

}