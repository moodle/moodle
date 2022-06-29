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

use tool_brickfield\local\htmlchecker\brickfield_accessibility;
use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_test;
use tool_brickfield\manager;

/**
 * Brickfield accessibility HTML checker library.
 *
 * Suspicious link text.
 * 'a' (anchor) element cannot contain any of the following text, such as (English): "click here".
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class a_suspicious_link_text extends brickfield_accessibility_test {
    /**
     * @var int The default severity code for this test.
     */
    public $defaultseverity = brickfield_accessibility::BA_TEST_SEVERE;

    /**
     * The main check function. This is called by the parent class to actually check content
     */
    public function check(): void {
        // Need to process all enabled lang versions of invalidlinkphrases.
        $badtext = brickfield_accessibility_test::get_all_invalidlinkphrases();

        foreach ($this->get_all_elements('a') as $a) {
            if (in_array(strtolower(trim($a->nodeValue)), $badtext) || $a->nodeValue == $a->getAttribute('href')) {
                // If the link text matches invalid phrases.
                $this->add_report($a);
            } else if (brickfield_accessibility::match_urls($a->nodeValue, $a->getAttribute('href'))) {
                // If the link text is the same as the link URL.
                $this->add_report($a);
            }
        }
    }

    /**
     * Return all 'a' elements.
     *
     * @return array
     */
    public function search(): array {
        $data = [];
        foreach ($this->get_all_elements('a') as $a) {
            $data[] = $a;
        }

        return $data;
    }
}
