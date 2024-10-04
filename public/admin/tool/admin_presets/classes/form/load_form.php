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
 * Form for loading settings.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load_form extends moodleform {

    public function definition(): void {
        $mform = &$this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $html = '<div class="alert alert-info">' . get_string('applypresetdescription', 'tool_admin_presets') . '</div>';
        $mform->addElement('html', $html);

        $this->add_action_buttons(true, get_string('loadselected', 'tool_admin_presets'));
    }
}
