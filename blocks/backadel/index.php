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
 * Backadel index page.
 *
 * @package    block_backadel
 * @copyright  2008 onwards - Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();

// Check to make sure the site admin is using the page.
if (!is_siteadmin($USER->id)) {
    print_error('need_permission', 'block_backadel');
}

// Begin page Setup.
$blockname = get_string('pluginname', 'block_backadel');
$header = get_string('build_search', 'block_backadel');

// Context setup.
$context = context_system::instance();
$PAGE->set_context($context);

// Build the page and Moodle requirements.
$PAGE->navbar->add($header);
$PAGE->set_title($blockname);
$PAGE->set_heading($SITE->shortname . ': ' . $blockname);
$PAGE->set_url('/blocks/backadel/index.php');
$PAGE->requires->js('/blocks/backadel/js/jquery.js');
$PAGE->requires->js('/blocks/backadel/js/index.js');

// Output the page header.
echo $OUTPUT->header();
echo $OUTPUT->heading($header);
echo html_writer::tag('div', '', array(
    'id' => 'results_error', 'class' => 'backadel_error'
));

// Enumerate the initial options.
$options = array('ALL', 'ANY');

// Set up the page controls.
$controls = html_writer::tag('div',
    get_string('match', 'block_backadel') .
    html_writer::select(array_combine($options, $options), 'type', 'ALL', null) .
    get_string('of_these_constraints', 'block_backadel') .
    html_writer::empty_tag('img',
        array('src' => 'images/delete.svg', 'class' => 'delete_constraint icon')) .
    html_writer::empty_tag('img',
        array('src' => 'images/add.svg', 'class' => 'add_constraint icon')),
    array('id' => 'anyall_row'));

// Grab more options.
$options = array(
    get_string('shortname'), get_string('fullname'),
    get_string('course_id', 'block_backadel'), get_string('category')
);

// Combine the options.
$options = array_combine($options, $options);
$crit = html_writer::select($options, 'c0_criteria', '', null);

// Add some more options.
$options = array(
    get_string('is', 'block_backadel'),
    get_string('is_not', 'block_backadel'),
    get_string('contains', 'block_backadel'),
    get_string('does_not_contain', 'block_backadel'));

// Combine the options.
$options = array_combine($options, $options);
$op = html_writer::select($options, 'c0_operator', '', null);

// Build the search UI.
$span = html_writer::tag('span',
    html_writer::empty_tag('input',
    array('name' => 'c0_search_term_0', 'type' => 'text')) .
    html_writer::empty_tag('img',
    array('src' => 'images/add.svg', 'class' => 'add_search_term icon')) .
    html_writer::empty_tag('input',
    array('id' => 'c0_st_num', 'value' => 1, 'type' => 'hidden')),
    array('id' => 'c0_search_term_0'));

$group = html_writer::tag('div',
    html_writer::tag('div', $crit . $op . $span,
    array('class' => 'constraint', 'id' => 'c0_constraint')),
    array('id' => 'group_constraints'));

$button = html_writer::tag('div',
    html_writer::empty_tag('input', array(
        'type' => 'submit', 'value' => get_string('build_search_button', 'block_backadel')
    )), array('id' => 'button')
);

// Set up the form container div.
$formcontainer = html_writer::tag('div', $controls . $group . $button, array(
    'id' => 'form_container'
));

// Output the form.
echo html_writer::tag('form', $formcontainer, array(
    'id' => 'query', 'action' => 'results.php', 'method' => 'POST'
));

// Output the course footer.
echo $OUTPUT->footer();
