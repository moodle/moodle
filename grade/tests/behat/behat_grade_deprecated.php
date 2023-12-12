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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Gherkin\Node\TableNode;

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2023 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grade_deprecated extends behat_deprecated_base {

    /**
     * Enters a quick feedback via the gradebook for a specific grade item and user when viewing
     * the 'Grader report' with editing mode turned on.
     *
     * @deprecated since 4.2 - we don't allow edit feedback on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Given /^I give the feedback "(?P<grade_number>(?:[^"]|\\")*)" to the user "(?P<username_string>(?:[^"]|\\")*)" for the grade item "(?P<grade_activity_string>(?:[^"]|\\")*)"$/
     * @param string $feedback
     * @param string $userfullname the user's fullname as returned by fullname()
     * @param string $itemname
     */
    public function i_give_the_feedback($feedback, $userfullname, $itemname) {
        $this->deprecated_message(['behat_grade::i_give_the_feedback']);

        $gradelabel = $userfullname . ' ' . $itemname;
        $fieldstr = get_string('useractivityfeedback', 'gradereport_grader', $gradelabel);

        $this->execute('behat_forms::i_set_the_field_to', array($this->escape($fieldstr), $this->escape($feedback)));
    }
}
