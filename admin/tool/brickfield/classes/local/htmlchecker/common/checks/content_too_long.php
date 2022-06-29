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
 * Test counts words for all text elements on page and suggests content chunking for pages longer than 3000 words.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_too_long extends brickfield_accessibility_test {

    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SUGGESTION;

    /**
     * The main check function. This is called by the parent class to actually check content.
     */
    public function check(): void {
        global $contentlengthlimit;

        $contentlengthlimit = 500;
        $pagetext = '';
        // There will be only one, but array is returned anyway.
        foreach ($this->get_all_elements('body') as $element) {
            $text = $element->textContent;
            if ($text != null) {
                $pagetext = $pagetext . $text;
            }
        }

        $wordcount = count_words($pagetext);
        if ($wordcount > $contentlengthlimit) {
            $this->add_report(null, "<p id='wc'>Word Count: " . $wordcount . "</p>", false);
        }
    }
}
