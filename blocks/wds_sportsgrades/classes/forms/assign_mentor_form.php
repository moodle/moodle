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
 * Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class assign_mentor_form extends moodleform {

    // Build the form.
    public function definition() {
        global $DB;

        // Get our settings.
        $s = get_config('block_wds_sportsgrades');

        // Instantiate the form.
        $mform = $this->_form;

        // Add the area for adding user / sport associations.
        $mform->addElement('header', 'assignmentorheader', get_string('assignmentor', 'block_wds_sportsgrades'));

        // Set the context.
        $context = context_system::instance();

        // Get the filter values from the form.
        $firstname = $this->_customdata['firstname'] ?? '';
        $lastname = $this->_customdata['lastname'] ?? '';

        $parms = [];

        // Add conditions for first name and last name.
        $conditions = '';

        if (!empty($firstname)) {
            $conditions .= 'AND u.firstname LIKE :firstname';
            $parms['firstname'] = '%' . $firstname . '%';
        }

        if (!empty($lastname)) {
            $conditions .= 'AND u.lastname LIKE :lastname';
            $parms['lastname'] = '%' . $lastname . '%';
        }

        // Get potential mentors by capability.
        $pmentors = get_users_by_capability(
            $context,
            'block/wds_sportsgrades:viewgrades',
            'u.*',
            'u.lastname ASC, u.firstname ASC',
        );

        // Build out the options array for the form.
        $useroptions = [];

        // Loop through the potential mentors to build the form.
        foreach ($pmentors as $pmentor) {
            $useroptions[$pmentor->id] = fullname($pmentor) . ' (' . $pmentor->username . ')';
        }

        $stusql = "SELECT u.*,
            stu.id AS studentid,
            u.firstname,
            u.lastname, 
            s.code AS sportcode,
            GROUP_CONCAT(DISTINCT(s.name) SEPARATOR ', ') AS sportname
            FROM mdl_user u
            INNER JOIN mdl_enrol_wds_students stu
                ON stu.userid = u.id
            INNER JOIN mdl_enrol_wds_students_meta sm
                ON sm.studentid = stu.id
                AND sm.datatype = 'Athletic_Team_ID'
            INNER JOIN mdl_enrol_wds_sport s
                ON s.code = sm.data
            INNER JOIN mdl_enrol_wds_periods per
                ON per.academic_period_id = sm.academic_period_id
                AND per.start_date - (86400 * $s->daysprior) < UNIX_TIMESTAMP()
                AND per.end_date + (86400 * $s->daysafter) > UNIX_TIMESTAMP()
            WHERE s.code NOT LIKE 'LSUE_%'
                $conditions
            GROUP BY u.id
            ORDER BY u.lastname ASC, u.firstname ASC";

        $pstudents = $DB->get_records_sql($stusql, $parms);

        if (empty($pstudents)) {
            redirect(
                new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php'),
                get_string('wds_sportsgrades:nostudents', 'block_wds_sportsgrades'),
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }

        // Add the mentor selector.
        $mform->addElement('select', 'mentorid', get_string('mentor', 'block_wds_sportsgrades'), $useroptions);

        // Loop through the potential mentors to build the form.
        foreach ($pstudents as $pstudent) {
            $studentoptions[$pstudent->id] = fullname($pstudent) . ' (' . $pstudent->sportname . ')';
        }

        // Add the student selector.
        $mform->addElement('select', 'studentadd', get_string('students', 'block_wds_sportsgrades'), $studentoptions);
        $mform->getElement('studentadd')->setMultiple(true);

        // Add the action buttons for adding.
        $this->add_action_buttons(true, get_string('assignmentor', 'block_wds_sportsgrades'));

        // Add the area for filtering.
        $mform->addElement('header', 'filterheader', get_string('filter', 'block_wds_sportsgrades'));

        // Add first name filter.
        $mform->addElement('text', 'firstname', get_string('search_firstname', 'block_wds_sportsgrades'));
        $mform->setType('firstname', PARAM_ALPHA);

        // Add last name filter.
        $mform->addElement('text', 'lastname', get_string('search_lastname', 'block_wds_sportsgrades'));
        $mform->setType('lastname', PARAM_ALPHA);

        // Add the filter button.
        $mform->addElement('submit', 'filterbutton', get_string('filter', 'block_wds_sportsgrades'));

        $mform->addElement('header', 'removementorheader', get_string('removementor', 'block_wds_sportsgrades'));

        // SQL to get the list of assigned mentors.
        $existingsql = 'SELECT ma.id AS assignid,
                            mentor.id AS mentorid,
                            mentor.firstname AS mentorfirstname,
                            mentor.lastname AS mentorlastname,
                            mentor.username AS mentorusername,
                            student.id AS studentid,
                            student.firstname AS studentfirstname,
                            student.lastname AS studentlastname,
                            student.username AS studentusername
                        FROM {block_wds_sportsgrades_mentor} ma
                        INNER JOIN {user} mentor ON ma.mentorid = mentor.id
                        INNER JOIN {user} student ON ma.userid = student.id
                        ORDER BY mentorlastname ASC,
                            mentorfirstname ASC,
                            studentlastname ASC,
                            studentfirstname ASC';

        // Fetch the data.
        $eusers = $DB->get_records_sql($existingsql);

        if (!empty($eusers)) {
            // Group users by mentor.
            $mentorgroups = [];
            foreach ($eusers as $euser) {
                $mentorname = $euser->mentorfirstname . ' ' . $euser->mentorlastname;
                if (!isset($mentorgroups[$mentorname])) {
                    $mentorgroups[$mentorname] = [];
                }
                $mentorgroups[$mentorname][] = $euser;
            }

            // Create a table for each Mentor.
            foreach ($mentorgroups as $mentorname => $mentors) {

                // Add a header for the sport.
                $mform->addElement('header', $mentorname, $mentorname);

                // Instantiate the table for this sport.
                $table = new html_table();

                // Give it a class.
                $table->attributes = ['class' => 'generaltable sportstable'];

                // Build out the head.
                $table->head = [
                    get_string('students', 'block_wds_sportsgrades'),
//                    get_string('mentor', 'block_wds_sportsgrades'),
                    get_string('action'),
                ];

                // Loop through the users for this sport.
                foreach ($mentors as $euser) {

                    // Create mentor and student objects for the fullname() function.
                    $mentor = new stdClass();
                    $mentor->id = $euser->mentorid;
                    $mentor->firstname = $euser->mentorfirstname;
                    $mentor->lastname = $euser->mentorlastname;

                    $student = new stdClass();
                    $student->id = $euser->studentid;
                    $student->firstname = $euser->studentfirstname;
                    $student->lastname = $euser->studentlastname;

                    // Build out the remove url with parms.
                    $removeurl = new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php',
                        ['userremove' => $euser->assignid, 'sesskey' => sesskey()]);

                    // Create the link HTML directly.
                    $removelink = html_writer::link($removeurl, get_string('remove'),
                        ['class' => 'btn btn-secondary']);

                    // Wrap it in the div.
                    $removebutton = html_writer::div($removelink, 'sportsbutton');

                    // Build the table row.
                    $row = [
                        $student->firstname . ' ' . $student->lastname . ' (' . $euser->studentusername . ')',
//                        $mentor->firstname . ' ' . $mentor->lastname . ' (' . $euser->mentorusername . ')',
                        $removebutton,
                    ];

                    // Add the above row to the table data array.
                    $table->data[] = $row;
                }

                // Add the table.
                $mform->addElement('html', html_writer::table($table));
            }
        }
    }
}
