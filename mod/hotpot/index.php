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
 * Display a list of HotPot activities with links to HotPot reports.
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourseid');
}

require_course_login($course);

hotpot_add_to_log($course->id, 'hotpot', 'view all', "index.php?id=$course->id", '');

$PAGE->set_url('/mod/hotpot/index.php', array('id' => $course->id));
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->shortname);
$PAGE->navbar->add(get_string('modulenameplural', 'mod_hotpot'));

/// Output starts here

echo $OUTPUT->header();

/// Get all the appropriate data

if (! $hotpots = get_all_instances_in_course('hotpot', $course)) {
    echo $OUTPUT->heading(get_string('nohotpots', 'mod_hotpot'), 2);
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
    die();
}

// get list of hotpot ids
$hotpotids = array();
foreach ($hotpots as $hotpot) {
    $hotpotids[] = $hotpot->id;
}

// get total number of attempts, users and details for these hotpots
if (has_capability('mod/hotpot:reviewallattempts', $PAGE->context)) {
    $show_aggregates = true;
    $single_user = false;
} else if (has_capability('mod/hotpot:reviewmyattempts', $PAGE->context)) {
    $show_aggregates = true;
    $single_user = true;
} else {
    $show_aggregates = false;
    $single_user = true;
}

if ($show_aggregates) {
    $params = array();
    $tables = '{hotpot_attempts} ha';
    $fields = 'ha.hotpotid AS hotpotid, COUNT(DISTINCT ha.clickreportid) AS attemptcount, COUNT(DISTINCT ha.userid) AS usercount, ROUND(SUM(ha.score) / COUNT(ha.score), 0) AS averagescore, MAX(ha.score) AS maxscore';
    $select = 'ha.hotpotid IN ('.implode(',', $hotpotids).')';
    if ($single_user) {
        // restrict results to this user only
        $select .= ' AND ha.userid=:userid';
        $params['userid'] = $USER->id;
    }
    $aggregates = $DB->get_records_sql("SELECT $fields FROM $tables WHERE $select GROUP BY ha.hotpotid", $params);
} else {
    $aggregates = array();
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    if (method_exists('course_modinfo', 'get_section_info_all')) {
        // Moodle >= 2.3
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
    } else {
        // Moodle 2.0 - 2.2
        $sections = get_all_sections($course->id);
    }
}

/// Print the list of instances (your module will probably extend this)

$strsectionname = get_string('sectionname', 'format_'.$course->format);
$strname        = get_string('name');
$strhighest     = get_string('gradehighest', 'quiz');
$straverage     = get_string('gradeaverage', 'quiz');
$strattempts    = get_string('attempts', 'quiz');

$table = new html_table();

if ($usesections) {
    $table->head  = array($strsectionname, $strname, $strhighest, $straverage, $strattempts);
    $table->align = array('center', 'left', 'center', 'center', 'left');
} else {
    $table->head  = array($strname, $strhighest, $straverage, $strattempts);
    $table->align = array('left', 'center', 'center', 'left');
}

foreach ($hotpots as $hotpot) {
    $row = new html_table_row();

    if ($usesections) {
        $text = get_section_name($course, $sections[$hotpot->section]);
        $row->cells[] = new html_table_cell($text);
    }

    if ($hotpot->visible) {
        $class = '';
    } else {
        $class = 'dimmed';
    }

    $href = new moodle_url('/mod/hotpot/view.php', array('id' => $hotpot->coursemodule));
    $params = array('href' => $href, 'class' => $class);

    $text = html_writer::tag('a', $hotpot->name, $params);
    $row->cells[] = new html_table_cell($text);

    // Create an object to represent this attempt at the current HotPot activity
    $cm = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);
    $hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context);

    if (empty($aggregates[$hotpot->id]) || empty($aggregates[$hotpot->id]->attemptcount)) {
        $row->cells[] = new html_table_cell('0'); // average score
        $row->cells[] = new html_table_cell('0'); // max score
        $row->cells[] = new html_table_cell('&nbsp;'); // reports
    } else {
        $reviewoptions = $hotpot->can_reviewhotpot();

        $href = new moodle_url('/mod/hotpot/report.php', array('id' => $hotpot->cm->id));
        $params = array('href' => $href, 'class' => $class);

        $text = $aggregates[$hotpot->id]->maxscore;
        if ($reviewoptions) {
            $text = html_writer::tag('a', $text, $params);
        }
        $row->cells[] = new html_table_cell($text);

        $text = $aggregates[$hotpot->id]->averagescore;
        if ($reviewoptions) {
            $text = html_writer::tag('a', $text, $params);
        }
        $row->cells[] = new html_table_cell($text);

        if ($reviewoptions) {
            $text = $aggregates[$hotpot->id]->usercount;
            $text = get_string('viewreports', 'mod_hotpot', $text);
            $text = html_writer::tag('a', $text, $params);
        } else {
            $text = '&nbsp;';
        }
        $row->cells[] = new html_table_cell($text);
    }

    $table->data[] = $row;
}

echo $OUTPUT->heading(get_string('modulenameplural', 'mod_hotpot'), 2);
echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();
