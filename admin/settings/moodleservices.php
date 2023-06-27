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
 * This file gives information about Moodle Services
 *
 * @package    core
 * @copyright  2018 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Create Moodle Services information.
    $moodleservices->add(new admin_setting_heading('moodleservicesintro', '',
        new lang_string('moodleservices_help', 'admin')));

    // Moodle Partners information.
    if (empty($CFG->disableserviceads_partner)) {
        $moodleservices->add(new admin_setting_heading('moodlepartners',
            new lang_string('moodlepartners', 'admin'),
            new lang_string('moodlepartners_help', 'admin')));
    }

    // Moodle app information.
    $moodleservices->add(new admin_setting_heading('moodleapp',
        new lang_string('moodleapp', 'admin'),
        new lang_string('moodleapp_help', 'admin')));

    // Branded Moodle app information.
    if (empty($CFG->disableserviceads_branded)) {
        $moodleservices->add(new admin_setting_heading('moodlebrandedapp',
            new lang_string('moodlebrandedapp', 'admin'),
            new lang_string('moodlebrandedapp_help', 'admin')));
    }
}


