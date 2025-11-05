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
 * Sports Grades search page.
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wds_sportsgrades;

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/wds_sportsgrades/classes/search.php');
require_once($CFG->dirroot . '/blocks/wds_sportsgrades/classes/forms/search_form.php');
require_once($CFG->libdir . '/tablelib.php');

// Page setup.
$PAGE->set_url(new \moodle_url('/blocks/wds_sportsgrades/view.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('page_title', 'block_wds_sportsgrades'));
$PAGE->set_heading(get_string('page_title', 'block_wds_sportsgrades'));
$PAGE->set_pagelayout('standard');

// Add breadcrumbs.
$PAGE->navbar->add(
    get_string('home'),
    new \moodle_url('/')
);

$PAGE->navbar->add(
    get_string('search_page_link', 'block_wds_sportsgrades'),
    new \moodle_url('/blocks/wds_sportsgrades/view.php')
);

// Check access.
require_login();
require_capability('block/wds_sportsgrades:view', \context_system::instance());

// Create the search form.
$search_form = new \block_wds_sportsgrades_search_form();

// Start output.
echo $OUTPUT->header();

// Display the search form.
$search_form->display();

// Initialize the search.
$search = new search();

// Process form submission.
if ($data = $search_form->get_data()) {
    // Convert form data to object for search.
    $search_params = new \stdClass();
    $search_params->universal_id = $data->universal_id;
    $search_params->username = $data->username;
    $search_params->firstname = $data->firstname;
    $search_params->lastname = $data->lastname;
    $search_params->major = $data->major;
    $search_params->classification = $data->classification;
    $search_params->sport = $data->sport;

    // Perform search.
    $results = $search::search_students($search_params);

    // Display results if search was successful.
    if (!empty($results['success']) && !empty($results['results'])) {
        echo \html_writer::tag('h4', get_string('search_results', 'block_wds_sportsgrades'));

        // Create a standard HTML table instead of using table_sql.
        $table = new \html_table();
        $table->head = [
            get_string('result_username', 'block_wds_sportsgrades'),
            get_string('result_universal_id', 'block_wds_sportsgrades'),
            get_string('result_firstname', 'block_wds_sportsgrades'),
            get_string('result_lastname', 'block_wds_sportsgrades'),
            get_string('result_college', 'block_wds_sportsgrades'),
            get_string('result_major', 'block_wds_sportsgrades'),
            get_string('result_classification', 'block_wds_sportsgrades'),
            get_string('result_sports', 'block_wds_sportsgrades'),
            get_string('result_view_grades', 'block_wds_sportsgrades')
        ];
        $table->attributes['class'] = 'table table-striped table-hover generaltable';

        foreach ($results['results'] as $student) {
            // Format the sports column.
            $sports_output = '';
            if (!empty($student->sports)) {
                foreach ($student->sports as $sport) {
                    $sports_output .= \html_writer::tag('span', $sport->name,
                        ['class' => 'badge badge-info m-1']);
                }
            }

            // Create the actions column with View Grades link.
            $url = new \moodle_url('/blocks/wds_sportsgrades/view_grades.php',
                ['studentid' => $student->studentid]);
            $actions = \html_writer::link(
                $url,
                get_string('result_view_grades', 'block_wds_sportsgrades'),
                ['class' => 'sportsgrades btn btn-primary btn-sm']
            );

            // Add the row to the table.
            $table->data[] = [
                $student->username,
                $student->universal_id,
                $student->firstname,
                $student->lastname,
                $student->college,
                $student->major,
                preg_replace('/^\S+\s+/', '', $student->classification),
                $sports_output,
                $actions
            ];
        }

        // Output the table.
        echo \html_writer::table($table);
    } else if (!empty($results['error'])) {
        echo \html_writer::div($results['error'], 'alert alert-danger');
    } else {
        echo \html_writer::div(get_string('search_no_results', 'block_wds_sportsgrades'), 'alert alert-info');
    }
}

// End output.
echo $OUTPUT->footer();
