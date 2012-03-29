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
 * Scheduled allocator's settings
 *
 * @package     workshopallocation_scheduled
 * @subpackage  mod_workshop
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once(dirname(dirname(__FILE__)) . '/random/settings_form.php'); // parent form

/**
 * Allocator settings form
 *
 * This is used by {@see workshop_scheduled_allocator::ui()} to set up allocation parameters.
 */
class workshop_scheduled_allocator_form extends workshop_random_allocator_form {

    /**
     * Definition of the setting form elements
     */
    public function definition() {
        $mform = $this->_form;
        $workshop = $this->_customdata['workshop'];

        $mform->addElement('header', 'scheduledsettings', get_string('pluginname', 'workshopallocation_scheduled'));
        $mform->addHelpButton('scheduledsettings', 'scheduledsettings', 'workshopallocation_scheduled');

        $mform->addElement('static', 'submissionendinfo', get_string('submissionend', 'workshop'),
            workshop::timestamp_formats($workshop->submissionend)->datetime);

        $mform->addElement('checkbox', 'enablescheduled', get_string('enablescheduled', 'workshopallocation_scheduled'), get_string('enablescheduledinfo', 'workshopallocation_scheduled'), 1);
        parent::definition();
    }
}
