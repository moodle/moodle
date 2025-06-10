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
 * @package    block_backadel
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_login();

// Ensure only the site admin accesses this page.
if (!is_siteadmin($USER->id)) {
    print_error('need_permission', 'block_backadel');
}

// Page Setup.
$blockname = get_string('pluginname', 'block_backadel');
$header = get_string('search_results', 'block_backadel');
$context = context_system::instance();
$PAGE->set_context($context);

// Set up the page header and Moodle requirements.
$PAGE->navbar->add($header);
$PAGE->set_title($blockname);
$PAGE->set_heading($SITE->shortname . ': ' . $blockname);
$PAGE->set_url('/blocks/backadel/results.php');
$PAGE->requires->js('/blocks/backadel/js/jquery.js');
$PAGE->requires->js('/blocks/backadel/js/toggle.js');

// Output the page header.
echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$cleandata = array();

// Redirect to the main page if there was nothing submitted.
if (!$data = data_submitted()) {
    redirect(new moodle_url('/blocks/backadel/index.php'));
}

// Loop through the data and clean it.
foreach ($data as $key => $value) {
    $cleandata[$key] = clean_param($value, PARAM_CLEAN);
}

// Set up the SQL.
$query = new stdClass;
$query->userid = $USER->id;
$query->type = $cleandata['type'] == 'ALL' ? 'AND' : 'OR';
$query->created_at = time();
$constraintdata = array();

// Loop through the cleaned data and build key value pairs for later use.
foreach ($cleandata as $key => $value) {
    if ($key[0] == 'c' && is_numeric($key[1])) {
        $i = $key[1];

        if (empty($constraintdata[$i]) || !is_array($constraintdata[$i])) {
            $constraintdata[$i] = array();
            $constraintdata[$i]['search_terms'] = '';
        }

        if (substr($key, 3, 11) == 'search_term') {
            $constraintdata[$i]['search_terms'] .= '|' . $value;
        } else {
            $constraintdata[$i][substr($key, 3)] = $value;
        }
    }
}

// Set up the search criteria.
$criteria = array(
    get_string('shortname') => 'co.shortname',
    get_string('fullname') => 'co.fullname',
    get_string('course_id', 'block_backadel') => 'co.idnumber',
    get_string('category') => 'cat.name'
);

// Set up the search operators.
$operators = array(
    get_string('is', 'block_backadel') => 'IN',
    get_string('is_not', 'block_backadel') => 'NOT IN',
    get_string('contains', 'block_backadel') => 'LIKE',
    get_string('does_not_contain', 'block_backadel') => 'NOT LIKE'
);

$constraints = array();

// Loop through the constraintdata and build the constraints.
foreach ($constraintdata as $c) {
    $c['criteria'] = $criteria[$c['criteria']];
    $c['operator'] = $operators[$c['operator']];
    $c['search_terms'] = substr($c['search_terms'], 1);
    $constraints[] = (object) $c;
}

// Get the records based on the query and contraints.
$results = $DB->get_records_sql(build_sql_from_search($query, $constraints));

// Create a new Moodle table.
$table = new html_table();

// Set up the table headers.
$table->head = array(
    get_string('shortname'),
    get_string('fullname'),
    get_string('category'),
    get_string('backup')
);

$table->data = array();

// Loop through the results and populate the table.
foreach ($results as $r) {
    $url = new moodle_url('/course/view.php', array('id' => $r->id));
    $link = html_writer::link($url, $r->shortname);
    $backupcheckbox = html_writer::checkbox('backup[]', $r->id);
    $rowdata = array($link, $r->fullname, $r->category, $backupcheckbox);
    $table->data[] = $rowdata;
}

// Output the form.
echo '<form action = "backup.php" method = "POST">';
echo html_writer::table($table);
echo html_writer::link('#', get_string('toggle_all', 'block_backadel'), array('class' => 'backadel toggle_link'));
echo '    <input type = "submit" value = "' . get_string('backup_button', 'block_backadel') . '"/>';
echo '</form>';

// Output the page footer.
echo $OUTPUT->footer();
