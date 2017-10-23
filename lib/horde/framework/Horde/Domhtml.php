<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @package   Util
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Parse DOM data from HTML strings.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @package   Util
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Domhtml implements Iterator
{
    /**
     * DOM object.
     *
     * @var DOMDocument
     */
    public $dom;

    /**
     * Iterator status.
     *
     * @var array
     */
    protected $_iterator = null;

    /**
     * Original charset of data.
     *
     * @var string
     */
    protected $_origCharset;

    /**
     * Encoding tag added to beginning of output.
     *
     * @var string
     */
    protected $_xmlencoding = '';

    /**
     * Constructor.
     *
     * @param string $text     The text of the HTML document.
     * @param string $charset  The charset of the HTML document.
     *
     * @throws Exception
     */
    public function __construct($text, $charset = null)
    {
        if (!extension_loaded('dom')) {
            throw new Exception('DOM extension is not available.');
        }

        // Bug #9616: Make sure we have valid HTML input.
        if (!strlen($text)) {
            $text = '<html></html>';
        }

        $old_error = libxml_use_internal_errors(true);
        $this->dom = new DOMDocument();

        if (is_null($charset)) {
            /* If no charset given, charset is whatever libxml tells us the
             * encoding should be defaulting to 'iso-8859-1'. */
            $this->_loadHTML($text);
            $this->_origCharset = $this->dom->encoding
                ? $this->dom->encoding
                : 'iso-8859-1';
        } else {
            /* Convert/try with UTF-8 first. */
            $this->_origCharset = Horde_String::lower($charset);
            $this->_xmlencoding = '<?xml encoding="UTF-8"?>';
            $this->_loadHTML(
                $this->_xmlencoding . Horde_String::convertCharset($text, $charset, 'UTF-8')
            );

            if ($this->dom->encoding &&
                (Horde_String::lower($this->dom->encoding) != 'utf-8')) {
                /* Convert charset to what the HTML document says it SHOULD
                 * be. */
                $this->_loadHTML(
                    Horde_String::convertCharset($text, $charset, $this->dom->encoding)
                );
                $this->_xmlencoding = '';
            }
        }

        if ($old_error) {
            libxml_use_internal_errors(false);
        }

        /* Sanity checking: make sure we have the documentElement object. */
        if (!$this->dom->documentElement) {
            $this->dom->appendChild($this->dom->createElement('html'));
        }

        /* Remove old charset information. */
        $xpath = new DOMXPath($this->dom);
        $domlist = $xpath->query('/html/head/meta[@http-equiv="content-type"]');
        for ($i = $domlist->length; $i > 0; --$i) {
            $meta = $domlist->item($i - 1);
            $meta->parentNode->removeChild($meta);
        }
    }

    /**
     * Returns the HEAD element, or creates one if it doesn't exist.
     *
     * @return DOMElement  HEAD element.
     */
    public function getHead()
    {
        $head = $this->dom->getElementsByTagName('head');
        if ($head->length) {
            return $head->item(0);
        }

        $headelt = $this->dom->createElement('head');
        $this->dom->documentElement->insertBefore($headelt, $this->dom->documentElement->firstChild);

        return $headelt;
    }

    /**
     * Returns the BODY element, or creates one if it doesn't exist.
     *
     * @since 2.2.0
     *
     * @return DOMElement  BODY element.
     */
    public function getBody()
    {
        $body = $this->dom->getElementsByTagName('body');
        if ($body->length) {
            return $body->item(0);
        }

        $bodyelt = $this->dom->createElement('body');
        $this->dom->documentElement->appendChild($bodyelt);

        return $bodyelt;
    }

    /**
     * Returns the full HTML text in the original charset.
     *
     * @param array $opts  Additional options: (since 2.1.0)
     *   - charset: (string) Return using this charset. If set but empty, will
     *              return as currently stored in the DOM object.
     *   - metacharset: (boolean) If true, will add a META tag containing the
     *                  charset information.
     *
     * @return string  HTML text.
     */
    public function returnHtml(array $opts = array())
    {
        $curr_charset = $this->getCharset();
        if (strcasecmp($curr_charset, 'US-ASCII') === 0) {
            $curr_charset = 'UTF-8';
        }
        $charset = array_key_exists('charset', $opts)
            ? (empty($opts['charset']) ? $curr_charset : $opts['charset'])
            : $this->_origCharset;

        if (empty($opts['metacharset'])) {
            $text = $this->dom->saveHTML();
        } else {
            /* Add placeholder for META tag. Can't add charset yet because DOM
             * extension will alter output if it exists. */
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('http-equiv', 'content-type');
            $meta->setAttribute('horde_dom_html_charset', '');

            $head = $this->getHead();
            $head->insertBefore($meta, $head->firstChild);

            $text = str_replace(
                'horde_dom_html_charset=""',
                'content="text/html; charset=' . $charset . '"',
                $this->dom->saveHTML()
            );

            $head->removeChild($meta);
        }

        if (strcasecmp($curr_charset, $charset) !== 0) {
            $text = Horde_String::convertCharset($text, $curr_charset, $charset);
        }

        if (!$this->_xmlencoding ||
            (($pos = strpos($text, $this->_xmlencoding)) === false)) {
            return $text;
        }

        return substr_replace($text, '', $pos, strlen($this->_xmlencoding));
    }

    /**
     * Returns the body text in the original charset.
     *
     * @return string  HTML text.
     */
    public function returnBody()
    {
        $body = $this->getBody();
        $text = '';

        if ($body->hasChildNodes()) {
            foreach ($body->childNodes as $child) {
                $text .= $this->dom->saveXML($child);
            }
        }

        return Horde_String::convertCharset($text, 'UTF-8', $this->_origCharset);
    }

    /**
     * Get the charset of the DOM data.
     *
     * @since 2.1.0
     *
     * @return string  Charset of DOM data.
     */
    public function getCharset()
    {
        return $this->dom->encoding
            ? $this->dom->encoding
            : ($this->_xmlencoding ? 'UTF-8' : $this->_origCharset);
    }

    /**
     * Loads the HTML data.
     *
     * @param string $html  HTML data.
     */
    protected function _loadHTML($html)
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $mask = defined('LIBXML_PARSEHUGE')
                ? LIBXML_PARSEHUGE
                : 0;
            $mask |= defined('LIBXML_COMPACT')
                ? LIBXML_COMPACT
                : 0;
            $this->dom->loadHTML($html, $mask);
        } else {
            $this->dom->loadHTML($html);
        }
    }

    /* Iterator methods. */

    /**
     */
    public function current()
    {
        if ($this->_iterator instanceof DOMDocument) {
            return $this->_iterator;
        }

        $curr = end($this->_iterator);
        return $curr['list']->item($curr['i']);
    }

    /**
     */
    public function key()
    {
        return 0;
    }

    /**
     */
    public function next()
    {
        /* Iterate in the reverse direction through the node list. This allows
         * alteration of the original list without breaking things (foreach()
         * w/removeChild() may exit iteration after removal is complete. */

        if ($this->_iterator instanceof DOMDocument) {
            $this->_iterator = array();
            $curr = array();
            $node = $this->dom;
        } elseif (empty($this->_iterator)) {
            $this->_iterator = null;
            return;
        } else {
            $curr = &$this->_iterator[count($this->_iterator) - 1];
            $node = $curr['list']->item($curr['i']);
        }

        if (empty($curr['child']) &&
            ($node instanceof DOMNode) &&
            $node->hasChildNodes()) {
            $curr['child'] = true;
            $this->_iterator[] = array(
                'child' => false,
                'i' => $node->childNodes->length - 1,
                'list' => $node->childNodes
            );
        } elseif (--$curr['i'] < 0) {
            array_pop($this->_iterator);
            $this->next();
        } else {
            $curr['child'] = false;
        }
    }

    /**
     */
    public function rewind()
    {
        $this->_iterator = $this->dom;
    }

    /**
     */
    public function valid()
    {
        return !is_null($this->_iterator);
    }

}
