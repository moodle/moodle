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
 * Ad-hoc quiz upgrade plugin.
 *
 * Allows the attempt data for quizzes that were not upgraded during the main
 * upgrade to be upgraded at any time.
 *
 * This screen lists all the quizzes that still need to be upgraded.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

// Start the page.
admin_externalpage_setup('reportquizupgrade');
admin_externalpage_print_header();

$quizzes = report_quizupgrade_get_upgradable_quizzes();

if (empty($quizzes)) {
    print_heading(get_string('alreadydone', 'report_quizupgrade'));

} else {
    print_heading(get_string('quizzeswithunconverted', 'report_quizupgrade'));
    print_box(get_string('intro', 'report_quizupgrade'));

    $table = new stdClass;
    $table->head = array(
        get_string('quizid', 'report_quizupgrade'),
        get_string('course'),
        get_string('modulename', 'quiz'),
        get_string('attemptstoconvert', 'report_quizupgrade'),
        get_string('actions', 'report_quizupgrade'),
    );

    foreach ($quizzes as $quiz) {
        $table->data[] = array(
            $quiz->id,
            '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $quiz->courseid .
                    '">' . format_string($quiz->shortname) . '</a>',
            '<a href="' . $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz->id .
                    '">' . format_string($quiz->name) . '</a>',
            $quiz->numtoconvert,
            '<a href="' . report_quizupgrade_url('convertquiz.php?quizid=' . $quiz->id) .
                    '">' . get_string('convertattempts', 'report_quizupgrade') . '</a>',
        );
    }

    print_table($table);
}

echo '<p><a href="' . report_quizupgrade_url('resetindex.php') . '">' .
        get_string('gotoresetlink', 'report_quizupgrade') . '</a></p>';

admin_externalpage_print_footer();
