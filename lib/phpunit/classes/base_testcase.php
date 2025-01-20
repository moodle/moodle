<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Base test case class.
 *
 * @package    core
 * @category   test
 * @author     Tony Levi <tony.levi@blackboard.com>
 * @copyright  2015 Blackboard (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;

/**
 * Base class for PHPUnit test cases customised for Moodle
 *
 * It is intended for functionality common to both basic and advanced_testcase.
 *
 * @package    core
 * @category   test
 * @author     Tony Levi <tony.levi@blackboard.com>
 * @copyright  2015 Blackboard (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_testcase extends PHPUnit\Framework\TestCase {
    // phpcs:disable
    // Following code is legacy code from phpunit to support assertTag
    // and assertNotTag.

    /**
     * Note: we are overriding this method to remove the deprecated error
     * @see https://tracker.moodle.org/browse/MDL-47129
     *
     * @param  array   $matcher
     * @param  string  $actual
     * @param  string  $message
     * @param  boolean $ishtml
     *
     * @deprecated 3.0
     */
    public static function assertTag($matcher, $actual, $message = '', $ishtml = true) {
        if ($ishtml) {
            $dom = self::loadHTML($actual);
        } else {
            $dom = (new PHPUnit\Util\Xml\Loader)->load($actual);
        }
        $tags = self::findNodes($dom, $matcher, $ishtml);
        $matched = (is_array($tags) && count($tags) > 0) && $tags[0] instanceof DOMNode;
        self::assertTrue($matched, $message);
    }

    /**
     * Load HTML into a DomDocument.
     *
     * Note: THis is a replacement for functionality removed from PHPUnit 10.
     *
     * @param string $actual
     * @throws \PHPUnit\Util\Xml\XmlException
     * @return DOMDocument
     */
    public static function loadHTML(string $actual): DOMDocument {
        if ($actual === '') {
            throw new \PHPUnit\Util\Xml\XmlException('Could not load XML from empty string');
        }

        $document = new DOMDocument;
        $document->preserveWhiteSpace = false;

        $internal  = libxml_use_internal_errors(true);
        $message   = '';
        $reporting = error_reporting(0);

        $loaded = $document->loadHTML($actual);

        foreach (libxml_get_errors() as $error) {
            $message .= "\n" . $error->message;
        }

        libxml_use_internal_errors($internal);
        error_reporting($reporting);

        if ($loaded === false) {
            if ($message === '') {
                $message = 'Could not load XML for unknown reason';
            }

            throw new \PHPUnit\Util\Xml\XmlException($message);
        }

        return $document;
    }

    /**
     * Note: we are overriding this method to remove the deprecated error
     * @see https://tracker.moodle.org/browse/MDL-47129
     *
     * @param  array   $matcher
     * @param  string  $actual
     * @param  string  $message
     * @param  boolean $ishtml
     *
     * @deprecated 3.0
     */
    public static function assertNotTag($matcher, $actual, $message = '', $ishtml = true) {
        if ($ishtml) {
            $dom = self::loadHTML($actual);
        } else {
            $dom = (new PHPUnit\Util\Xml\Loader)->load($actual);
        }
        $tags = self::findNodes($dom, $matcher, $ishtml);
        $matched = (is_array($tags) && count($tags) > 0) && $tags[0] instanceof DOMNode;
        self::assertFalse($matched, $message);
    }

    /**
     * Validate list of keys in the associative array.
     *
     * @param array $hash
     * @param array $validKeys
     *
     * @return array
     *
     * @throws PHPUnit\Framework\Exception
     */
    public static function assertValidKeys(array $hash, array $validKeys) {
        $valids = array();

        // Normalize validation keys so that we can use both indexed and
        // associative arrays.
        foreach ($validKeys as $key => $val) {
            is_int($key) ? $valids[$val] = null : $valids[$key] = $val;
        }

        $validKeys = array_keys($valids);

        // Check for invalid keys.
        foreach ($hash as $key => $value) {
            if (!in_array($key, $validKeys)) {
                $unknown[] = $key;
            }
        }

        if (!empty($unknown)) {
            throw new PHPUnit\Framework\Exception(
                'Unknown key(s): ' . implode(', ', $unknown)
            );
        }

        // Add default values for any valid keys that are empty.
        foreach ($valids as $key => $value) {
            if (!isset($hash[$key])) {
                $hash[$key] = $value;
            }
        }

        return $hash;
    }

    /**
     * Assert that two Date/Time strings are equal.
     *
     * The strings generated by \DateTime, \strtotime, \date, \time, etc. are generated outside of our control.
     * From time-to-time string changes are made.
     * One such example is from ICU 72.1 which changed the time format to include a narrow-non-breaking-space (U+202F)
     * between the time and AM/PM.
     *
     * We should not update our tests to match these changes, as it is not our code that is
     * generating the strings and they may change again.
     * In addition, the changes are not equal amongst all systems as they depend on the version of ICU installed.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    public function assertEqualsIgnoringWhitespace($expected, $actual, string $message = ''): void {
        // ICU 72.1 introduced the use of a narrow-non-breaking-space (U+202F) between the time and the AM/PM.
        // Normalise all whitespace when performing the comparison.
        $expected = preg_replace('/\s+/u', ' ', $expected);
        $actual = preg_replace('/\s+/u', ' ', $actual);

        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Parse out the options from the tag using DOM object tree.
     *
     * @param DOMDocument $dom
     * @param array       $options
     * @param bool        $isHtml
     *
     * @return array
     */
    public static function findNodes(DOMDocument $dom, array $options, $isHtml = true) {
        $valid = array(
            'id', 'class', 'tag', 'content', 'attributes', 'parent',
            'child', 'ancestor', 'descendant', 'children', 'adjacent-sibling'
        );

        $filtered = array();
        $options  = self::assertValidKeys($options, $valid);

        // find the element by id
        if ($options['id']) {
            $options['attributes']['id'] = $options['id'];
        }

        if ($options['class']) {
            $options['attributes']['class'] = $options['class'];
        }

        $nodes = array();

        // find the element by a tag type
        if ($options['tag']) {
            if ($isHtml) {
                $elements = self::getElementsByCaseInsensitiveTagName(
                    $dom,
                    $options['tag']
                );
            } else {
                $elements = $dom->getElementsByTagName($options['tag']);
            }

            foreach ($elements as $element) {
                $nodes[] = $element;
            }

            if (empty($nodes)) {
                return false;
            }
        } // no tag selected, get them all
        else {
            $tags = array(
                'a', 'abbr', 'acronym', 'address', 'area', 'b', 'base', 'bdo',
                'big', 'blockquote', 'body', 'br', 'button', 'caption', 'cite',
                'code', 'col', 'colgroup', 'dd', 'del', 'div', 'dfn', 'dl',
                'dt', 'em', 'fieldset', 'form', 'frame', 'frameset', 'h1', 'h2',
                'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'i', 'iframe',
                'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'link',
                'map', 'meta', 'noframes', 'noscript', 'object', 'ol', 'optgroup',
                'option', 'p', 'param', 'pre', 'q', 'samp', 'script', 'select',
                'small', 'span', 'strong', 'style', 'sub', 'sup', 'table',
                'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title',
                'tr', 'tt', 'ul', 'var',
                // HTML5
                'article', 'aside', 'audio', 'bdi', 'canvas', 'command',
                'datalist', 'details', 'dialog', 'embed', 'figure', 'figcaption',
                'footer', 'header', 'hgroup', 'keygen', 'mark', 'meter', 'nav',
                'output', 'progress', 'ruby', 'rt', 'rp', 'track', 'section',
                'source', 'summary', 'time', 'video', 'wbr'
            );

            foreach ($tags as $tag) {
                if ($isHtml) {
                    $elements = self::getElementsByCaseInsensitiveTagName(
                        $dom,
                        $tag
                    );
                } else {
                    $elements = $dom->getElementsByTagName($tag);
                }

                foreach ($elements as $element) {
                    $nodes[] = $element;
                }
            }

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by attributes
        if ($options['attributes']) {
            foreach ($nodes as $node) {
                $invalid = false;

                foreach ($options['attributes'] as $name => $value) {
                    // match by regexp if like "regexp:/foo/i"
                    if (preg_match('/^regexp\s*:\s*(.*)/i', $value, $matches)) {
                        if (!preg_match($matches[1], $node->getAttribute($name))) {
                            $invalid = true;
                        }
                    } // class can match only a part
                    elseif ($name == 'class') {
                        // split to individual classes
                        $findClasses = explode(
                            ' ',
                            preg_replace("/\s+/", ' ', $value)
                        );

                        $allClasses = explode(
                            ' ',
                            preg_replace("/\s+/", ' ', $node->getAttribute($name))
                        );

                        // make sure each class given is in the actual node
                        foreach ($findClasses as $findClass) {
                            if (!in_array($findClass, $allClasses)) {
                                $invalid = true;
                            }
                        }
                    } // match by exact string
                    else {
                        if ($node->getAttribute($name) !== (string) $value) {
                            $invalid = true;
                        }
                    }
                }

                // if every attribute given matched
                if (!$invalid) {
                    $filtered[] = $node;
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by content
        if ($options['content'] !== null) {
            foreach ($nodes as $node) {
                $invalid = false;

                // match by regexp if like "regexp:/foo/i"
                if (preg_match('/^regexp\s*:\s*(.*)/i', $options['content'], $matches)) {
                    if (!preg_match($matches[1], self::getNodeText($node))) {
                        $invalid = true;
                    }
                } // match empty string
                elseif ($options['content'] === '') {
                    if (self::getNodeText($node) !== '') {
                        $invalid = true;
                    }
                } // match by exact string
                elseif (strstr(self::getNodeText($node), $options['content']) === false) {
                    $invalid = true;
                }

                if (!$invalid) {
                    $filtered[] = $node;
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by parent node
        if ($options['parent']) {
            $parentNodes = self::findNodes($dom, $options['parent'], $isHtml);
            $parentNode  = isset($parentNodes[0]) ? $parentNodes[0] : null;

            foreach ($nodes as $node) {
                if ($parentNode !== $node->parentNode) {
                    continue;
                }

                $filtered[] = $node;
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by child node
        if ($options['child']) {
            $childNodes = self::findNodes($dom, $options['child'], $isHtml);
            $childNodes = !empty($childNodes) ? $childNodes : array();

            foreach ($nodes as $node) {
                foreach ($node->childNodes as $child) {
                    foreach ($childNodes as $childNode) {
                        if ($childNode === $child) {
                            $filtered[] = $node;
                        }
                    }
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by adjacent-sibling
        if ($options['adjacent-sibling']) {
            $adjacentSiblingNodes = self::findNodes($dom, $options['adjacent-sibling'], $isHtml);
            $adjacentSiblingNodes = !empty($adjacentSiblingNodes) ? $adjacentSiblingNodes : array();

            foreach ($nodes as $node) {
                $sibling = $node;

                while ($sibling = $sibling->nextSibling) {
                    if ($sibling->nodeType !== XML_ELEMENT_NODE) {
                        continue;
                    }

                    foreach ($adjacentSiblingNodes as $adjacentSiblingNode) {
                        if ($sibling === $adjacentSiblingNode) {
                            $filtered[] = $node;
                            break;
                        }
                    }

                    break;
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by ancestor
        if ($options['ancestor']) {
            $ancestorNodes = self::findNodes($dom, $options['ancestor'], $isHtml);
            $ancestorNode  = isset($ancestorNodes[0]) ? $ancestorNodes[0] : null;

            foreach ($nodes as $node) {
                $parent = $node->parentNode;

                while ($parent && $parent->nodeType != XML_HTML_DOCUMENT_NODE) {
                    if ($parent === $ancestorNode) {
                        $filtered[] = $node;
                    }

                    $parent = $parent->parentNode;
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by descendant
        if ($options['descendant']) {
            $descendantNodes = self::findNodes($dom, $options['descendant'], $isHtml);
            $descendantNodes = !empty($descendantNodes) ? $descendantNodes : array();

            foreach ($nodes as $node) {
                foreach (self::getDescendants($node) as $descendant) {
                    foreach ($descendantNodes as $descendantNode) {
                        if ($descendantNode === $descendant) {
                            $filtered[] = $node;
                        }
                    }
                }
            }

            $nodes    = $filtered;
            $filtered = array();

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by children
        if ($options['children']) {
            $validChild   = array('count', 'greater_than', 'less_than', 'only');
            $childOptions = self::assertValidKeys(
                $options['children'],
                $validChild
            );

            foreach ($nodes as $node) {
                $childNodes = $node->childNodes;

                foreach ($childNodes as $childNode) {
                    if ($childNode->nodeType !== XML_CDATA_SECTION_NODE &&
                        $childNode->nodeType !== XML_TEXT_NODE) {
                        $children[] = $childNode;
                    }
                }

                // we must have children to pass this filter
                if (!empty($children)) {
                    // exact count of children
                    if ($childOptions['count'] !== null) {
                        if (count($children) !== $childOptions['count']) {
                            break;
                        }
                    } // range count of children
                    elseif ($childOptions['less_than']    !== null &&
                        $childOptions['greater_than'] !== null) {
                        if (count($children) >= $childOptions['less_than'] ||
                            count($children) <= $childOptions['greater_than']) {
                            break;
                        }
                    } // less than a given count
                    elseif ($childOptions['less_than'] !== null) {
                        if (count($children) >= $childOptions['less_than']) {
                            break;
                        }
                    } // more than a given count
                    elseif ($childOptions['greater_than'] !== null) {
                        if (count($children) <= $childOptions['greater_than']) {
                            break;
                        }
                    }

                    // match each child against a specific tag
                    if ($childOptions['only']) {
                        $onlyNodes = self::findNodes(
                            $dom,
                            $childOptions['only'],
                            $isHtml
                        );

                        // try to match each child to one of the 'only' nodes
                        foreach ($children as $child) {
                            $matched = false;

                            foreach ($onlyNodes as $onlyNode) {
                                if ($onlyNode === $child) {
                                    $matched = true;
                                }
                            }

                            if (!$matched) {
                                break 2;
                            }
                        }
                    }

                    $filtered[] = $node;
                }
            }

            $nodes = $filtered;

            if (empty($nodes)) {
                return;
            }
        }

        // return the first node that matches all criteria
        return !empty($nodes) ? $nodes : array();
    }

    /**
     * Recursively get flat array of all descendants of this node.
     *
     * @param DOMNode $node
     *
     * @return array
     */
    protected static function getDescendants(DOMNode $node) {
        $allChildren = array();
        $childNodes  = $node->childNodes ? $node->childNodes : array();

        foreach ($childNodes as $child) {
            if ($child->nodeType === XML_CDATA_SECTION_NODE ||
                $child->nodeType === XML_TEXT_NODE) {
                continue;
            }

            $children    = self::getDescendants($child);
            $allChildren = array_merge($allChildren, $children, array($child));
        }

        return isset($allChildren) ? $allChildren : array();
    }

    /**
     * Gets elements by case insensitive tagname.
     *
     * @param DOMDocument $dom
     * @param string      $tag
     *
     * @return DOMNodeList
     */
    protected static function getElementsByCaseInsensitiveTagName(DOMDocument $dom, $tag) {
        $elements = $dom->getElementsByTagName(strtolower($tag));

        if ($elements->length == 0) {
            $elements = $dom->getElementsByTagName(strtoupper($tag));
        }

        return $elements;
    }

    /**
     * Get the text value of this node's child text node.
     *
     * @param DOMNode $node
     *
     * @return string
     */
    protected static function getNodeText(DOMNode $node) {
        if (!$node->childNodes instanceof DOMNodeList) {
            return '';
        }

        $result = '';

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE ||
                $childNode->nodeType === XML_CDATA_SECTION_NODE) {
                $result .= trim($childNode->data) . ' ';
            } else {
                $result .= self::getNodeText($childNode);
            }
        }

        return str_replace('  ', ' ', $result);
    }

    /**
     * Helper to get the count of invocation.
     *
     * This is required because the method to use changed names in PHPUnit 10.0 in a breaking change.
     *
     * @param \PHPUnit\Framework\MockObject\Rule\InvocationOrder $counter
     * @return int
     */
    protected static function getInvocationCount(InvocationOrder $counter): int {
        if (method_exists($counter, 'numberOfInvocations')) {
            return $counter->numberOfInvocations();
        }

        return $counter->getInvocationCount();
    }
    // phpcs:enable

    /**
     * Get an invokable object for testing.
     *
     * This is a helper method to create an invokable object for testing which can be used to
     * track invocations, including arguments provided.
     *
     * This can be useful for modifications to the error handler.
     *
     * @return object
     */
    protected static function get_invokable() {
        return new class {
            private array $invocations = [];
            public function __invoke(...$args) {
                $this->invocations[] = $args;
            }

            public function get_invocations(): array {
                return $this->invocations;
            }

            public function get_invocation_count(): int {
                return count($this->invocations);
            }

            public function reset(): void {
                $this->invocations = [];
            }
        };
    }

    /**
     * Determine whether the test is running in isolation.
     *
     * Note: This was previously a public method of the TestCase, but as removed in PHPUnit 10.
     * There is no direct replacement, but we can use reflection to access the protected property.
     * @return bool
     */
    public function isInIsolation(): bool {
        $rc = new \ReflectionClass(TestCase::class);
        $rcp = $rc->getProperty('inIsolation');

        return $rcp->getValue($this);
    }
}
