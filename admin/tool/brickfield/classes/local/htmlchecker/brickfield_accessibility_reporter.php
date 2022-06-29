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

namespace tool_brickfield\local\htmlchecker;

use stdClass;

/**
 * The base class for a reporter
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_reporter {
    /** @var object The current document's DOMDocument */
    public $dom;

    /** @var object The current brickfieldaccessibilitycss object */
    public $css;

    /** @var array An array of test names and the translation for the problems with it */
    public $translation;

    /** @var array A collection of ReportItem objects */
    public $report;

    /** @var array The path to the current document */
    public $path;

    /** @var object Additional options for this reporter */
    public $options;

    /** @var array An array of attributes to search for to turn into absolute paths rather than relative paths */
    public $absoluteattributes = ['src', 'href'];

    /**
     * The class constructor
     * @param object $dom The current DOMDocument object
     * @param object $css The current brickfield CSS object
     * @param object $guideline The current guideline object
     * @param string $path The current path
     */
    public function __construct(&$dom, &$css, &$guideline, $path = '') {
        $this->dom = &$dom;
        $this->css = &$css;
        $this->path = $path;
        $this->options = new stdClass;
        $this->guideline = &$guideline;
    }

    /**
     * Sets options for the reporter
     * @param array $options an array of options
     */
    public function set_options(array $options) {
        foreach ($options as $key => $value) {
            $this->options->$key = $value;
        }
    }

    /**
     * Sets the absolute path for an element
     * @param object $element A DOMElement object to turn into an absolute path
     */
    public function set_absolute_path(&$element) {
        $attr = false;
        foreach ($this->absoluteattributes as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $attr = $attribute;
            }
        }

        if ($attr) {
            $item = $element->getAttribute($attr);
            // We are ignoring items with absolute URLs.
            if (strpos($item, '://') === false) {
                $item = implode('/', $this->path) . ltrim($item, '/');
                $element->setAttribute($attr, $item);
            }
        }
    }
}
