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

/**
 * Base test class for tests which checks that the given input tag has an associated lable tag.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_has_label extends brickfield_accessibility_test {

    // To override, just override the tag and type variables, and use $no_type = true if it is a special form tag like textarea.

    /** @var string The tag name that this test applies to */
    public $tag = 'input';

    /** @var string The type of input tag this is */
    public $type = 'text';

    /** @var bool Wehether or not we should check the type attribute of the input tags */
    public $notype = false;

    /**
     * Iterate through all the elemetns using the $tag tagname and the $type attribute (if appropriate)
     * and then check it against a list of all LABEL tags.
     */
    public function check() {
        foreach ($this->get_all_elements('label') as $label) {
            if ($label->hasAttribute('for')) {
                $labels[$label->getAttribute('for')] = $label;
            } else {
                foreach ($label->childNodes as $child) {
                    if (property_exists($child, 'tagName') &&
                        ($child->tagName == $this->tag) &&
                        (($child->getAttribute('type') == $this->type) || $this->notype)) {
                        $inputinlabel[$child->getAttribute('name')] = $child;
                    }
                }
            }
        }
        foreach ($this->get_all_elements($this->tag) as $input) {
            if ($input->getAttribute('type') == $this->type || $this->notype) {
                if (!$input->hasAttribute('title')) {
                    if (!isset($inputinlabel[$input->getAttribute('name')])) {
                        if (!isset($labels[$input->getAttribute('id')]) ||
                            (trim($labels[$input->getAttribute('id')]->nodeValue) == '')) {
                            $this->add_report($input);
                        }
                    }
                }
            }
        }
    }
}
