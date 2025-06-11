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
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('classes/lib.php');

require_login();

$courseid = required_param('id', PARAM_INT);

// Filters.
$sectionid = optional_param('sectionid', null, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);
$ln = optional_param('ln', null, PARAM_TEXT);
$fn = optional_param('fn', null, PARAM_TEXT);

// Sorts.
$order = optional_param('order', null, PARAM_TEXT);
$by = optional_param('dir', 'DESC', PARAM_TEXT);

$courseparams = array('id' => $courseid);

$course = $DB->get_record('course', $courseparams);
if (empty($course)) {
    moodle_exception('no_course', 'block_ues_logs');
}

$context = context_course::instance($course->id);
if (!has_capability('moodle/grade:edit', $context)) {
    moodle_exception('no_permission', 'block_ues_logs');
}

$s = ues::gen_str('block_ues_logs');

$blockname = $s('pluginname');
$PAGE->set_url('/blocks/ues_logs/view.php', $courseparams);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_heading($blockname);
$PAGE->set_title($blockname);
$PAGE->navbar->add($blockname);
$PAGE->set_pagetype('block_ues_logs');

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

$sections = ues_section::from_course($course);

$courselabel = function ($course, $section) {
    return "$course->department $course->cou_number $section->sec_number";
};

$tooption = function ($section) use ($courselabel) {
    $course = $section->course();
    return $courselabel($course, $section);
};

$sectionselector = array(0 => $s('allsections')) + array_map($tooption, $sections);

$baseurl = new moodle_url('view.php', array(
    'id' => $courseid,
    'sectionid' => $sectionid,
    'action' => $action,
    'ln' => $ln,
    'fn' => $fn,
    'order' => $order,
    'dir' => $by
));

$nothing = array('' => $s('section'));
echo $OUTPUT->single_select($baseurl, 'sectionid', $sectionselector, $sectionid, $nothing);

$nothing = array('' => $s('action'));
$defaults = array(0 => $s('both'));
$actions = $defaults + array(ues_log::ADD => $s('add'), ues_log::DROP => $s('drop'));

echo $OUTPUT->single_select($baseurl, 'action', $actions, $action, $nothing);

echo initial_bar($baseurl->params(), $ln);

$totables = function ($in, $section) use ($s, $courselabel, $OUTPUT, $baseurl) {
    $urlparams = $baseurl->params();

    $byparams = array('l.sectionid' => $section->id);

    $actionfilter = $urlparams['action'];
    if ($actionfilter) {
        $byparams['l.action'] = $actionfilter;
    }

    $firstnamefilter = $urlparams['fn'];
    if ($firstnamefilter) {
        $byparams['u.firstname'] = $firstnamefilter;
    }

    $lastnamefilter = $urlparams['ln'];
    if ($lastnamefilter) {
        $byparams['u.lastname'] = $lastnamefilter;
    }

    $order = $urlparams['order'];
    $by = $urlparams['dir'];
    if ($order) {
        $orderby = "$order $by";
    } else {
        $orderby = 'timestamp DESC';
    }

    $logs = ues_log::get_by_special($byparams, $orderby);
    $count = count($logs);

    $nhead = get_string('firstname') . ' / '. get_string('lastname');

    $table = new html_table();
    $table->head = array($nhead, get_string('action'), get_string('time'));
    $table->data = array();

    foreach ($logs as $log) {
        $name = fullname($log);
        $emaillink = html_writer::link('mailto:' . $log->email, $name);

        $class = $log->action == 'AD' ? 'add' : 'drop';
        $action = '<span class = "table_'.$class.'">' . $log->action . '</span>';
        $timestamp = (int) $log->timestamp;

        $line = array($emaillink, $action, core_date::strftime('%F %T', $timestamp));
        $table->data[] = new html_table_row($line);
    }

    echo $OUTPUT->heading($courselabel($section->course(), $section));

    echo '<div class = "tracking_table">' .
            html_writer::table($table) .
         '</div>';

    return $in or !empty($count);
};

if ($sectionid) {
    $sections = ues_section::get_all(array('id' => $sectionid));
}

$success = array_reduce($sections, $totables, false);

if (!$success) {
    $OUTPUT->notification($s('no_logs'));
}

echo initial_bar($baseurl->params(), $ln);

echo $OUTPUT->footer();

function initial_bar($params, $chosen) {
    $strall = get_string('all');

    $html = '<div class="initialbar lastinitial">'. get_string('lastname'). ' : ';

    $makelink = function ($value, $letter=null) use ($params) {
        if (is_null($letter)) {
            $letter = $value;
        }

        $params['ln'] = $letter;

        return html_writer::link(new moodle_url('view.php', $params), $value);
    };

    $bold = function ($text) {
        return '<strong>' . $text . '</strong>';
    };

    if (!empty($chosen)) {
        $html .= $makelink($strall, '');
    } else {
        $html .= $bold($strall);
    }

    $alpha = explode(',', get_string('alphabet', 'langconfig'));
    foreach ($alpha as $letter) {
        if ($letter == $chosen) {
            $html .= ' ' . $bold($letter);
        } else {
            $html .= ' ' . $makelink($letter);
        }
    }

    $html .= '</div>';
    return $html;
}
