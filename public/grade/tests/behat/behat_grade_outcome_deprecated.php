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
 * Deprecated steps definitions related with grade outcomes.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

use Behat\Gherkin\Node\TableNode;

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo       MDL-86636 This will be deleted in Moodle 7.0.
 */
class behat_grade_outcome_deprecated extends behat_deprecated_base {
    /**
     * Downloads the file from a named button or link and verifies its type and content.
     *
     * @deprecated since 6.0
     *
     * @Then following :buttontext button should download an outcome file that:
     * @Then following :linktext link should download an outcome file that:
     *
     * @param string $text the text of the button or link.
     * @param TableNode $table the table of assertions used to check the file contents.
     */
    public function following_button_should_download_an_outcome_file_that(string $text, TableNode $table): void {
        $this->deprecated_message([
            'behat_download::following_should_download_a_file_that() - ' .
                'following "' . $text . '" should download a file that:',
            'behat_download::following_element_should_download_a_file_that() - ' .
                'following "' . $text . '" "button" should download a file that:',
        ]);
    }
}
