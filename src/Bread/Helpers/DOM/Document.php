<?php
/**
 * Bread PHP Framework (http://github.com/saiv/Bread)
 * Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 *
 * Licensed under a Creative Commons Attribution 3.0 Unported License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 * @link       http://github.com/saiv/Bread Bread PHP Framework
 * @package    Bread
 * @since      Bread PHP Framework
 * @license    http://creativecommons.org/licenses/by/3.0/
 */
namespace Bread\Helpers\DOM;

use DOMImplementation, DOMDocument, DOMXPath;

class Document
{

    protected $implementation;

    protected $doctype;

    protected $document;

    public $xpath;

    public $root;

    public function __construct($qualifiedName, $namespace = null, $publicId = null, $systemId = null)
    {
        $this->implementation = new DOMImplementation();
        $this->doctype = $this->implementation->createDocumentType($qualifiedName, $publicId, $systemId);
        $this->document = $this->implementation->createDocument($namespace, $qualifiedName, $this->doctype);
        $this->document->encoding = 'utf-8';
        $this->document->formatOutput = true;
        $this->document->preserveWhiteSpace = false;
        $this->xpath = new DOMXPath($this->document);
        $this->root = new Node($this, $this->document->documentElement);
    }

    public function __invoke($name, $context = null)
    {
        if (preg_match('/^</', $name)) {
            $fragment = $this->document->createDocumentFragment();
            $fragment->appendXML($name);
            $node = $fragment->removeChild($fragment->firstChild);
            return new Node($this, $node);
        }
        return $this->query($name, $context);
    }

    public function __toString()
    {
        return $this->save();
    }

    public function query($query, $context = null)
    {
        $context = $context ?  : $this->root;
        $nodes = array();
        foreach ($context->nodes as $node) {
            foreach ($this->xpath->query($query, $node) as $n) {
                $nodes[] = $n;
            }
        }
        return new Node($this, $nodes);
    }

    public function load($filename, $options = 0)
    {
        if (preg_match('/^</', $filename)) {
            $this->document->loadXML($filename, $options);
        } else {
            $this->document->load($filename, $options);
        }
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace($this->document->documentElement->prefix, $this->document->documentElement->namespaceURI);
        $this->root = new Node($this, $this->document->documentElement);
    }

    public function create($name, $value = null, $attributes = array(), $namespace = null)
    {
        $element = $namespace ? $this->document->createElementNS($namespace, $name, $value) : $this->document->createElement($name, $value);
        foreach ($attributes as $name => $value) {
            if (false === $value) {
                continue;
            }
            $element->setAttribute($name, $value);
        }
        return new Node($this, $element);
    }

    public function save($node = null, $options = 0)
    {
        return $this->document->saveXML($node, $options);
    }
}
