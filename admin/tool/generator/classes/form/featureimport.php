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

namespace tool_generator\form;

defined('MOODLE_INTERNAL') || die();

use moodleform;

global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form for importting a testing scenario feature file.
 *
 * @package          tool_generator
 * @copyright        2023 Ferran Recio <ferran@moodle.com>
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featureimport extends moodleform {
    public function definition(): void {
        $mform = &$this->_form;

        // File upload.
        $mform->addElement(
            'filepicker',
            'featurefile',
            get_string('testscenario_file', 'tool_generator'),
            null,
            ['accepted_types' => ['.feature']]
        );
        $mform->addRule('featurefile', null, 'required');

        $options = [
            0 => get_string('execute_scenarios', 'tool_generator'),
            1 => get_string('execute_cleanup', 'tool_generator'),
        ];
        $mform->addElement('select', 'executecleanup', get_string('execute', 'tool_generator'), $options);

        $this->add_action_buttons(false, get_string('import'));
    }

    /**
     * Get the feature file contents.
     * @return string|null the feature file contents or null if not found.
     */
    public function get_feature_contents(): ?string {
        $result = $this->get_file_content('featurefile');
        if (!$result) {
            return null;
        }
        return $result;
    }
}
