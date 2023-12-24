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

namespace tool_brickfield\local\htmlchecker\common;

use tool_brickfield\local\htmlchecker\brickfield_accessibility_report_item;
use tool_brickfield\manager;

/**
 * This handles importing DOM objects, adding items to the report and provides a few DOM-traversing methods
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_test {
    /** @var object The DOMDocument object */
    public $dom;

    /** @var object The brickfieldCSS object */
    public $css;

    /** @var array The path for the request */
    public $path;

    /** @var bool Whether the test can be used in a CMS (content without HTML head) */
    public $cms = true;

    /** @var string The base path for this request */
    public $basepath;

    /** @var array An array of ReportItem objects */
    public $report = array();

    /** @var int The fallback severity level for all tests */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SUGGESTION;

    /** @var array An array of all the extensions that are images */
    public $imageextensions = array('gif', 'jpg', 'png', 'jpeg', 'tiff', 'svn');

    /** @var string The language domain */
    public $lang = 'en';

    /** @var array An array of translatable strings */
    public $strings = array('en' => '');

    /** @var mixed Any additional options passed by htmlchecker. */
    public $options;

    /**
     * The class constructor. We pass items by reference so we can alter the DOM if necessary
     * @param object $dom The DOMDocument object
     * @param object $css The brickfieldCSS object
     * @param array $path The path of this request
     * @param string $languagedomain The langauge domain to user
     * @param mixed $options Any additional options passed by htmlchecker.
     */
    public function __construct(&$dom, &$css, &$path, $languagedomain = 'en', $options = null) {
        $this->dom = $dom;
        $this->css = $css;
        $this->path = $path;
        $this->lang = $languagedomain;
        $this->options = $options;
        $this->report = array();
        $this->check();
    }

    /**
     * Helper method to collect the report from this test. Some
     * tests do additional cleanup by overriding this method
     * @return array An array of ReportItem objects
     */
    public function get_report(): array {
        $this->report['severity'] = $this->defaultseverity;
        return $this->report;
    }

    /**
     * Returns the default severity of the test
     * @return int The severity level
     */
    public function get_severity(): int {
        return $this->defaultseverity;
    }

    /**
     * Adds a new ReportItem to this current tests collection of reports.
     * Most reports pertain to a particular element (like an IMG with no Alt attribute);
     * however, some are document-level and just either pass or don't pass
     * @param object $element The DOMElement object that pertains to this report
     * @param string $message An additional message to add to the report
     * @param bool $pass Whether or not this report passed
     * @param object $state Extra information about the error state
     * @param bool $manual Whether the report needs a manual check
     */
    public function add_report($element = null, $message = null, $pass = null, $state = null, $manual = null) {
        $report          = new brickfield_accessibility_report_item();
        $report->element = $element;
        $report->message = $message;
        $report->pass    = $pass;
        $report->state   = $state;
        $report->manual  = $manual;
        $report->line    = $report->get_line();
        $this->report[]  = $report;
    }

    /**
     * Retrieves the full path for a file.
     * @param string $file The path to a file
     * @return string The absolute path to the file.
     */
    public function get_path($file): string {
        if ((substr($file, 0, 7) == 'http://') || (substr($file, 0, 8) == 'https://')) {
            return $file;
        }
        $file = explode('/', $file);
        if (count($file) == 1) {
            return implode('/', $this->path) . '/' . $file[0];
        }

        $path = $this->path;
        foreach ($file as $directory) {
            if ($directory == '..') {
                array_pop($path);
            } else {
                $filepath[] = $directory;
            }
        }
        return implode('/', $path) .'/'. implode('/', $filepath);
    }

    /**
     * Returns a translated variable. If the translation is unavailable, English is returned
     * Because tests only really have one string array, we can get all of this info locally
     * @return mixed The translation for the object
     */
    public function translation() {
        if (isset($this->strings[$this->lang])) {
            return $this->strings[$this->lang];
        }
        if (isset($this->strings['en'])) {
            return $this->strings['en'];
        }
        return false;
    }

    /**
     * Helper method to find all the elements that fit a particular query
     * in the document (either by tag name, or by attributes from the htmlElements object)
     * @param mixed $tags Either a single tag name in a string, or an array of tag names
     * @param string $options The kind of option to select an element by (see htmlElements)
     * @param bool $value The value of the above option
     * @return array An array of elements that fit the description
     */
    public function get_all_elements($tags = null, string $options = '', bool $value = true): array {
        if (!is_array($tags)) {
            $tags = [$tags];
        }
        if ($options !== '') {
            $temp = new html_elements();
            $tags = $temp->get_elements_by_option($options, $value);
        }
        $result = [];

        if (!is_array($tags)) {
            return [];
        }
        foreach ($tags as $tag) {
            $elements = $this->dom->getElementsByTagName($tag);
            if ($elements) {
                foreach ($elements as $element) {
                    $result[] = $element;
                }
            }
        }
        if (count($result) == 0) {
            return [];
        }
        return $result;
    }

    /**
     * Returns true if an element has a child with a given tag name
     * @param object $element A DOMElement object
     * @param string $childtag The tag name of the child to find
     * @return bool TRUE if the element does have a child with
     *              the given tag name, otherwise FALSE
     */
    public function element_has_child($element, string $childtag): bool {
        foreach ($element->childNodes as $child) {
            if (property_exists($child, 'tagName') && $child->tagName == $childtag) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the first ancestor reached of a tag, or false if it hits
     * the document root or a given tag.
     * @param object $element A DOMElement object
     * @param string $ancestortag The name of the tag we are looking for
     * @param string $limittag Where to stop searching
     * @return bool
     */
    public function get_element_ancestor($element, string $ancestortag, string $limittag = 'body') {
        while (property_exists($element, 'parentNode')) {
            if ($element->parentNode->tagName == $ancestortag) {
                return $element->parentNode;
            }
            if ($element->parentNode->tagName == $limittag) {
                return false;
            }
            $element = $element->parentNode;
        }
        return false;
    }

    /**
     * Finds all the elements with a given tag name that has
     * an attribute
     * @param string $tag The tag name to search for
     * @param string $attribute The attribute to search on
     * @param bool $unique Whether we only want one result per attribute
     * @return array An array of DOMElements with the attribute
     *               value as the key.
     */
    public function get_elements_by_attribute(string $tag, string $attribute, bool $unique = false): array {
        $results = array();
        foreach ($this->get_all_elements($tag) as $element) {
            if ($element->hasAttribute($attribute)) {
                if ($unique) {
                    $results[$element->getAttribute($attribute)] = $element;
                } else {
                    $results[$element->getAttribute($attribute)][] = $element;
                }
            }
        }
        return $results;
    }

    /**
     * Returns the next element after the current one.
     * @param object $element A DOMElement object
     * @return mixed FALSE if there is no other element, or a DOMElement object
     */
    public function get_next_element($element) {
        $parent = $element->parentNode;
        $next = false;
        foreach ($parent->childNodes as $child) {
            if ($next) {
                return $child;
            }
            if ($child->isSameNode($element)) {
                $next = true;
            }
        }
        return false;
    }

    /**
     * To minimize notices, this compares an object's property to the valus
     * and returns true or false. False will also be returned if the object is
     * not really an object, or if the property doesn't exist at all
     * @param object $object The object too look at
     * @param string $property The name of the property
     * @param mixed $value The value to check against
     * @param bool $trim Whether the property value should be trimmed
     * @param bool $lower Whether the property value should be compared on lower case
     *
     * @return bool
     */
    public function property_is_equal($object, string $property, $value, bool $trim = false, bool $lower = false) {
        if (!is_object($object)) {
            return false;
        }
        if (!property_exists($object, $property)) {
            return false;
        }
        $propertyvalue = $object->$property;
        if ($trim) {
            $propertyvalue = trim($propertyvalue);
            $value = trim($value);
        }
        if ($lower) {
            $propertyvalue = strtolower($propertyvalue);
            $value = strtolower($value);
        }
        return ($propertyvalue == $value);
    }

    /**
     * Returns the parent of an elment that has a given tag Name, but
     * stops the search if it hits the $limiter tag
     * @param object $element The DOMElement object to search on
     * @param string $tagname The name of the tag of the parent to find
     * @param string $limiter The tag name of the element to stop searching on
     *               regardless of the results (like search for a parent "P" tag
     *               of this node but stop if you reach "body")
     * @return mixed FALSE if no parent is found, or the DOMElement object of the found parent
     */
    public function get_parent($element, string $tagname, string $limiter) {
        while ($element) {
            if ($element->tagName == $tagname) {
                return $element;
            }
            if ($element->tagName == $limiter) {
                return false;
            }
            $element = $element->parentNode;
        }
        return false;
    }

    /**
     * Returns if a GIF files is animated or not http://us.php.net/manual/en/function.imagecreatefromgif.php#88005
     * @param string $filename
     * @return int
     */
    public function image_is_animated($filename): int {
        if (!($fh = @fopen($filename, 'rb'))) {
            return false;
        }
        $count = 0;
        // An animated gif contains multiple "frames", with each frame having a
        // header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C).

        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers.
        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); // Read 100kb at a time.
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
        }

        fclose($fh);
        return $count > 1;
    }

    /**
     * Returns if there are any printable/readable characters within an element.
     * This finds both node values or images with alt text.
     * @param object $element The given element to look at
     * @return bool TRUE if contains readable text, FALSE if otherwise
     */
    public function element_contains_readable_text($element): bool {
        if (is_a($element, 'DOMText')) {
            if (trim($element->wholeText) != '') {
                return true;
            }
        } else {
            if (trim($element->nodeValue) != '' ||
                ($element->hasAttribute('alt') && trim($element->getAttribute('alt')) != '')) {
                    return true;
            }
            if (method_exists($element, 'hasChildNodes') && $element->hasChildNodes()) {
                foreach ($element->childNodes as $child) {
                    if ($this->element_contains_readable_text($child)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Returns an array of the newwindowphrases for all enabled language packs.
     * @return array of the newwindowphrases for all enabled language packs.
     */
    public static function get_all_newwindowphrases(): array {
        // Need to process all enabled lang versions of newwindowphrases.
        return static::get_all_phrases('newwindowphrases');
    }

    /**
     * Returns an array of the invalidlinkphrases for all enabled language packs.
     * @return array of the invalidlinkphrases for all enabled language packs.
     */
    public static function get_all_invalidlinkphrases(): array {
        // Need to process all enabled lang versions of invalidlinkphrases.
        return static::get_all_phrases('invalidlinkphrases');
    }

    /**
     * Returns an array of the relevant phrases for all enabled language packs.
     * @param string $stringname the language string identifier you want get the phrases for.
     * @return array of the invalidlinkphrases for all enabled language packs.
     */
    protected static function get_all_phrases(string $stringname): array {
        $stringmgr = get_string_manager();
        $allstrings = [];

        // Somehow, an invalid string was requested. Add exception handling for this in the future.
        if (!$stringmgr->string_exists($stringname, manager::PLUGINNAME)) {
            return $allstrings;
        }

        // Need to process all enabled lang versions of invalidlinkphrases.
        $enabledlangs = $stringmgr->get_list_of_translations();
        foreach ($enabledlangs as $lang => $value) {
            $tmpstring = (string)new \lang_string($stringname, manager::PLUGINNAME, null, $lang);
            $tmplangarray = explode('|', $tmpstring);
            $allstrings = array_merge($allstrings, $tmplangarray);
        }
        // Removing duplicates if a lang is enabled, yet using default 'en' due to no relevant lang file.
        $allstrings = array_unique($allstrings);
        return $allstrings;
    }

    /**
     * Assesses whether a string contains any readable text, which is text that
     * contains any characters other than whitespace characters.
     *
     * @param string $text
     * @return bool
     */
    public static function is_text_readable(string $text): bool {
        // These characters in order are a space, tab, line feed, carriage return,
        // NUL-byte, vertical tab and non-breaking space unicode character \xc2\xa0.
        $emptycharacters = " \t\n\r\0\x0B\xc2\xa0";
        return trim($text, $emptycharacters) != '';
    }
}
