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

namespace tool_admin_presets\form;

defined('MOODLE_INTERNAL') || die();

use moodleform;

global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form for importting settings.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_form extends moodleform {

    public function definition(): void {
        $mform = &$this->_form;

        // Rename preset input.
        $mform->addElement('text', 'name',
            get_string('renamepreset', 'tool_admin_presets'), 'maxlength="254" size="40"');
        $mform->setType('name', PARAM_TEXT);

        // File upload.
        $mform->addElement('filepicker', 'xmlfile', get_string('selectfile', 'tool_admin_presets'), null,
            ['accepted_types' => ['.xml']]);
        $mform->addRule('xmlfile', null, 'required');

        $this->add_action_buttons(true, get_string('import', 'tool_admin_presets'));
    }
}
