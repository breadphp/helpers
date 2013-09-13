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
namespace Bread\Helpers\HTML;

use Bread\Helpers\DOM;
use Bread\Helpers\CSS\Selector;
use DOMXPath;

class Page extends DOM\Document
{

    protected $composed = false;

    public function __construct($html = null, $path = null)
    {
        parent::__construct('html');
        if ($html) {
            if ($path) {
                $filename = $path . $html;
                if (file_exists($filename)) {
                    $html = $filename;
                }
            }
            $this->load($html);
        } else {
            $this->root = new Node($this, $this->root);
            $this->head = $this->root->append('head');
            $this->charset = $this->head->append('meta')->charset = 'utf-8';
            $this->title = $this->head->append('title');
            $this->body = $this->root->append('body');
        }
    }

    public function __invoke($name, $context = null)
    {
        return new Node($this, parent::__invoke($name, $context));
    }

    public function __toString()
    {
        return parent::__toString();
    }

    public function query($selector, $context = null)
    {
        $context = $context ?  : $this->root;
        $xpath = Selector::toXPath($selector);
        $nodes = array();
        foreach ($context->nodes as $node) {
            foreach ($this->xpath->query($xpath, $node) as $n) {
                $nodes[] = $n;
            }
        }
        return new Node($this, $nodes);
    }

    public function create($name, $value = null, $attributes = array())
    {
        $classes = explode('.', $name);
        $name = array_shift($classes);
        if (is_array($value)) {
            $attributes = $value;
            $value = null;
        }
        if (!empty($classes)) {
            if (isset($attributes['class'])) {
                $classes[] = $attributes['class'];
            }
            $attributes = array_merge($attributes, array(
                'class' => implode(" ", array_unique($classes))
            ));
        }
        list ($name, $id) = explode('#', $name) + array(
            null,
            null
        );
        if ($id) {
            $attributes['id'] = $id;
        }
        $element = $this->document->createElement($name);
        $element->appendChild($this->document->createCDATASection($value));
        foreach ($attributes as $name => $value) {
            if (false === $value) {
                continue;
            }
            $element->setAttribute($name, $value);
        }
        return new Node($this, $element);
    }

    public function load($filename, $options = LIBXML_NOXMLDECL)
    {
        libxml_use_internal_errors(true);
        $this->document->loadHTMLFile($filename);
        $this->xpath = new DOMXPath($this->document);
        $this->root = new Node($this, $this->document->documentElement);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
    }

    public function save($node = null, $options = LIBXML_NOXMLDECL)
    {
        return $this->document->saveHTML($node);
    }
}
