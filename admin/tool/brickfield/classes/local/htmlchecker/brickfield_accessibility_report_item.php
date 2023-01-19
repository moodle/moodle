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

use DOMDocument;

/**
 * A report item. There is one per issue with the report
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_report_item {

    /** @var object The DOMElement that the report item refers to (if any) */
    public $element;

    /** @var string The error message */
    public $message;

    /** @var bool Whether the check needs to be manually verified */
    public $manual;

    /** @var bool For document-level tests, this says whether the test passed or not */
    public $pass;

    /** @var object For issues with more than two possible states, this contains information about the state */
    public $state;

    /** @var int the line number of the report item */
    public $line;

    /**
     * Returns the line number of the report item. Unfortunately we can't use getLineNo
     * if we are before PHP 5.3, so if not we try to get the line number through a more
     * circuitous way.
     */
    public function get_line() {
        if (is_object($this->element) && method_exists($this->element, 'getLineNo')) {
            return $this->element->getLineNo();
        }
        return 0;
    }

    /**
     * Returns the current element in plain HTML form
     * @param array $extraattributes An array of extra attributes to add to the element
     * @return string An HTML string version of the provided DOMElement object
     */
    public function get_html(array $extraattributes = []): string {
        if (!$this->element) {
            return '';
        }

        $resultdom = new DOMDocument();
        $resultdom->formatOutput = true;
        $resultdom->preserveWhiteSpace = false;

        try {
            $resultelement = $resultdom->importNode($this->element, true);
        } catch (Exception $e) {
            return false;
        }

        foreach ($this->element->attributes as $attribute) {
            if ($attribute->name != 'brickfield_accessibility_style_index') {
                $resultelement->setAttribute($attribute->name, $attribute->value);
            }
        }

        foreach ($extraattributes as $name => $value) {
            $resultelement->setAttribute($name, $value);
        }

        @$resultdom->appendChild($resultelement);
        return @$resultdom->saveHTML();
    }
}
