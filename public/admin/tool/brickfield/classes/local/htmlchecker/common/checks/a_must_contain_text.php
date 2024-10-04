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

namespace tool_brickfield\local\htmlchecker\common\checks;

use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * Each source anchor contains text.
 * a (anchor) element must contain text. The text may occur in the anchor text or in the title attribute of the anchor
 * or in the Alt text of an image used within the anchor.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class a_must_contain_text extends brickfield_accessibility_test {
    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SEVERE;

    /**
     * The main check function. This is called by the parent class to actually check content.
     */
    public function check(): void {
        foreach ($this->get_all_elements('a') as $a) {
            if (!$this->element_contains_readable_text($a) && ($a->hasAttribute('href'))) {
                $this->add_report($a);
            }
        }
    }

    /**
     * Returns if a link is not a candidate to be an anchor (which does
     * not need text).
     * @param \DOMElement $a
     * @return bool Whether is is a link (TRUE) or an anchor (FALSE)
     */
    public function is_not_anchor(\DOMElement $a): bool {
        return (!($a->hasAttribute('name') && !$a->hasAttribute('href')));
    }
}
