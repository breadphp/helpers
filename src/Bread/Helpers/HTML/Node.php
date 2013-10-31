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

class Node extends DOM\Node implements Interfaces\Node
{

    public function __construct(Page $page, $name, $value = null, $attributes = array())
    {
        parent::__construct($page, $name, $value, $attributes);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->nodes[0]->getAttribute($name);
            default:
                return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'id':
                $this->nodes[0]->setAttribute($name, $value);
                $this->nodes[0]->setIdAttribute($name, true);
                break;
            default:
                parent::__set($name, $value);
        }
    }

    public function parent()
    {
        return new Node($this->document, $this->nodes[0]->parentNode);
    }
    
    public function siblings()
    {
        $siblings = array();
        $self = $this->nodes[0];
        foreach ($self->parentNode->childNodes as $sibling) {
            if ($sibling->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            if ($self === $sibling) {
                continue;
            }
            $siblings[] = $sibling;
        }
        return new Node($this->document, $siblings);
    }
    
    public function data($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $n => $v) {
                $n = "data-$n";
                $this->$n = $v;
            }
            return $this;
        }
        $name = "data-$name";
        if ($value) {
            $this->$name = $value;
            return $this;
        }
        return $this->$name;
    }

    /**
     * Adds the specified class(es) to each of the set of matched elements.
     */
    public function addClass($class)
    {
        foreach ($this->nodes as $node) {
            $classes = $this->getClasses($node);
            $classes[] = $class;
            $this->setClasses($node, $classes);
        }
        return $this;
    }

    /**
     * Determine whether any of the matched elements are assigned the given class.
     */
    public function hasClass($class)
    {
        foreach ($this->nodes as $node) {
            if (in_array($class, $this->getClasses($node))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a single class, multiple classes, or all classes from each element
     * in the set of matched elements.
     */
    public function removeClass($class)
    {
        foreach ($this->nodes as $node) {
            $this->setClasses($node, array_diff($this->getClasses($node), explode(' ', $class)));
        }
        return $this;
    }

    /**
     * Add or remove one or more classes from each element in the set of matched
     * elements, depending on either the class’s presence or the value of the
     * switch argument.
     */
    public function toggleClass($class)
    {
        $classes = explode(' ', $class);
        foreach ($classes as $class) {
            if ($this->hasClass($class)) {
                $this->removeClass($class);
            } else {
                $this->addClass($class);
            }
        }
        return $this;
    }

    /**
     * Get the HTML contents of the first element in the set of matched elements
     * or set the HTML contents of every matched element.
     */
    public function html($html)
    {
        if (file_exists($html)) {
            $id = uniqid('wrapper');
            $wrapper = sprintf('<div id="%s">%s</div>', $id, file_get_contents($html));
            $fragment = new Page($wrapper);
            foreach ($fragment(sprintf('#%s', $id)) as $node) {
                $this->append($node);
            }
        }
        return;
    }

    protected function getClasses($node)
    {
        $classes = $node->getAttribute('class');
        return $classes ? explode(' ', $classes) : array();
    }

    protected function setClasses($node, $array)
    {
        $class = implode(' ', array_unique($array));
        if ($class) {
            $node->setAttribute('class', $class);
        } elseif ($node->hasAttribute('class')) {
            $node->removeAttribute('class');
        }
    }

    public function query($query)
    {
        return $this->document->query($query, $this);
    }
}
