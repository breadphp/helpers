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
namespace Bread\Helpers\CSS\Selector;

/**
 * TokenStream represents a stream of CSS Selector tokens.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TokenStream
{

    private $used;

    private $tokens;

    private $source;

    private $peeked;

    private $peeking;

    /**
     * Constructor.
     *
     * @param array $tokens
     *            The tokens that make the stream.
     * @param mixed $source
     *            The source of the stream.
     */
    public function __construct($tokens, $source = null)
    {
        $this->used = array();
        $this->tokens = $tokens;
        $this->source = $source;
        $this->peeked = null;
        $this->peeking = false;
    }

    /**
     * Gets the tokens that have already been visited in this stream.
     *
     * @return array
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Gets the next token in the stream or null if there is none.
     * Note that if this stream was set to be peeking its behavior
     * will be restored to not peeking after this operation.
     *
     * @return mixed
     */
    public function next()
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;
            
            return $this->peeked;
        }
        
        if (!count($this->tokens)) {
            return null;
        }
        
        $next = array_shift($this->tokens);
        $this->used[] = $next;
        
        return $next;
    }

    /**
     * Peeks for the next token in this stream.
     * This means that the next token
     * will be returned but it won't be considered as used (visited) until the
     * next() method is invoked.
     * If there are no remaining tokens null will be returned.
     *
     * @see next()
     *
     * @return mixed
     */
    public function peek()
    {
        if (!$this->peeking) {
            if (!count($this->tokens)) {
                return null;
            }
            
            $this->peeked = array_shift($this->tokens);
            
            $this->peeking = true;
        }
        
        return $this->peeked;
    }
}
