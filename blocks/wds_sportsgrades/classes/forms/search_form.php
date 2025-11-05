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
 * Search form for Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Student search form
 */
class block_wds_sportsgrades_search_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $USER, $DB;

        // Instantiate the form.
        $mform = $this->_form;

        // Basic search fields.
        $mform->addElement('header', 'basic_search', get_string('search_title', 'block_wds_sportsgrades'));

        $mform->addElement('text', 'universal_id', get_string('search_universal_id', 'block_wds_sportsgrades'));
        $mform->setType('universal_id', PARAM_TEXT);

        $mform->addElement('text', 'username', get_string('search_username', 'block_wds_sportsgrades'));
        $mform->setType('username', PARAM_TEXT);

        // Build out the parm for limiting sport searches.
        $sportsparms = ['userid' => $USER->id];

        // Get list of sports from the database.
        $sportsql = 'SELECT sa.id AS said, COALESCE(s.id, 0) AS id, s.code, COALESCE(s.name, "All Sports") AS name
            FROM {block_wds_sportsgrades_access} sa
            LEFT JOIN {enrol_wds_sport} s ON sa.sportid = s.id
            WHERE sa.userid = :userid
            GROUP BY name ORDER BY name ASC';

        // Pre add this so admins do not error out.
        $sport_options = ['' => get_string('search_sport_all', 'block_wds_sportsgrades')];

        // Get the sports.
        $sports = $DB->get_records_sql($sportsql, $sportsparms);

        // Loop through the sports.
        foreach ($sports as $sport) {

            // Build out the sport code / name options.
            $sport_options[$sport->code] = $sport->name;
        }

        // Add the search.
        $mform->addElement('select', 'sport', get_string('search_sport', 'block_wds_sportsgrades'), $sport_options);

        // Advanced search fields.
        $mform->addElement('header', 'advanced_search', get_string('search_advanced', 'block_wds_sportsgrades'));
        $mform->setExpanded('advanced_search', false);

        $mform->addElement('text', 'firstname', get_string('search_firstname', 'block_wds_sportsgrades'));
        $mform->setType('firstname', PARAM_TEXT);

        $mform->addElement('text', 'lastname', get_string('search_lastname', 'block_wds_sportsgrades'));
        $mform->setType('lastname', PARAM_TEXT);

        $mform->addElement('text', 'major', get_string('search_major', 'block_wds_sportsgrades'));
        $mform->setType('major', PARAM_TEXT);

        // Build out the SQL to get classifications.
        $csql = "SELECT sm.data
            FROM {enrol_wds_students_meta} sm
            INNER JOIN {enrol_wds_students_meta} sm2
                ON sm.studentid = sm2.studentid
                AND sm2.datatype = 'Athletic_Team_ID'
            WHERE sm.datatype = 'Classification'
            GROUP BY sm.data
            ORDER BY FIELD(
                RIGHT(sm.data, LENGTH(sm.data) - INSTR(sm.data, ' ')),
                'Freshman',
                'First Year',
                '1L',
                'Sophomore',
                'Second Year',
                '2L',
                'Junior',
                'Third Year',
                '3L',
                'Senior',
                'Fourth Year',
                'Graduate')";

        // Get the list of classifications.
        $cobj = $DB->get_records_sql($csql, null);

        // Get the keys.
        $carray = array_keys($cobj);

        // clean up the array by stripping the institution.
        $trimmed = array_map(
            fn($key) => preg_replace('/^\S+\s+/', '', $key),
            $carray
        );

        // Build the array from the keys and trimmed names.
        $classifications = array_combine($carray, $trimmed);

        // Make sure we can search across all classifications.
        $classifications = ['' => 'All'] + $classifications;

        // Build out the select.
        $mform->addElement('select', 'classification', get_string('search_classification', 'block_wds_sportsgrades'), $classifications);

        // Add action buttons.
        $this->add_action_buttons(false, get_string('search_button', 'block_wds_sportsgrades'));
    }
}
