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
 * Import attendance sessions.
 *
 * @package mod_attendance
 * @author Chris Wharton <chriswharton@catalyst.net.nz>
 * @copyright 2017 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_attendance\form\import;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use moodleform;
require_once($CFG->libdir . '/formslib.php');

/**
 * Import attendance sessions.
 *
 * @package mod_attendance
 * @author Chris Wharton <chriswharton@catalyst.net.nz>
 * @copyright 2017 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sessions_confirm extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $importer = $this->_customdata;

        $mform = $this->_form;
        $mform->addElement('hidden', 'confirm', 1);
        $mform->setType('confirm', PARAM_BOOL);
        $mform->addElement('hidden', 'importid', $importer->get_importid());
        $mform->setType('importid', PARAM_INT);

        $requiredheaders = $importer->list_required_headers();
        $foundheaders = $importer->list_found_headers();

        if (empty($foundheaders)) {
            $foundheaders = range(0, count($requiredheaders));
        }
        $foundheaders[- 1] = get_string('none');

        foreach ($requiredheaders as $index => $requiredheader) {
            $mform->addElement('select', 'header' . $index, $requiredheader, $foundheaders);
            if (isset($foundheaders[$index])) {
                $mform->setDefault('header' . $index, $index);
            } else {
                $mform->setDefault('header' . $index, - 1);
            }
        }

        $this->add_action_buttons(true, get_string('confirm', 'mod_attendance'));
    }
}
