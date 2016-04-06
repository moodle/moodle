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
 * shows an analysed view of a feedback on the mainsite
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");

$current_tab = 'analysis';

$id = required_param('id', PARAM_INT);  //the POST dominated the GET
$courseitemfilter = optional_param('courseitemfilter', '0', PARAM_INT);
$courseitemfiltertyp = optional_param('courseitemfiltertyp', '0', PARAM_ALPHANUM);
$searchcourse = optional_param('searchcourse', '', PARAM_RAW);
$courseid = optional_param('courseid', false, PARAM_INT);

$url = new moodle_url('/mod/feedback/analysis_course.php', array('id'=>$id));
navigation_node::override_active_url($url);
if ($courseid !== false) {
    $url->param('courseid', $courseid);
}
if ($courseitemfilter !== '0') {
    $url->param('courseitemfilter', $courseitemfilter);
}
if ($courseitemfiltertyp !== '0') {
    $url->param('courseitemfiltertyp', $courseitemfiltertyp);
}
if ($searchcourse !== '') {
    $url->param('searchcourse', $searchcourse);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

if (!($feedback->publish_stats OR has_capability('mod/feedback:viewreports', $context))) {
    print_error('error');
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($feedback->name));

/// print the tabs
require('tabs.php');

//print the analysed items

if (has_capability('mod/feedback:viewreports', $context)) {
    //button "export to excel"
    echo $OUTPUT->container_start('form-buttons');
    $aurl = new moodle_url('analysis_to_excel.php', array('sesskey' => sesskey(), 'id' => $id,
                                 'coursefilter' => $courseid));
    echo $OUTPUT->single_button($aurl, get_string('export_to_excel', 'feedback'));
    echo $OUTPUT->container_end();
}

//get the groupid
//lstgroupid is the choosen id
$mygroupid = false;
//get completed feedbacks
$completedscount = feedback_get_completeds_group_count($feedback, $mygroupid, $courseid);

//show the count
echo '<b>'.get_string('completed_feedbacks', 'feedback').': '.$completedscount. '</b><br />';

// get the items of the feedback
$params = array('feedback' => $feedback->id, 'hasvalue' => 1);
$items = $DB->get_records('feedback_item', $params, 'position');
//show the count
if (is_array($items)) {
    echo '<b>'.get_string('questions', 'feedback').': ' .count($items). ' </b><hr />';
}

if ($courseitemfilter > 0) {
    $avgvalue = 'avg(value)';
    if ($DB->get_dbfamily() == 'postgres') { // TODO: this should be moved to standard sql DML function ;-)
         $avgvalue = 'avg(cast (value as integer))';
    }

    $sql = "SELECT fv.course_id, c.shortname, $avgvalue AS avgvalue
            FROM {feedback_value} fv, {course} c, {feedback_item} fi
            WHERE fv.course_id = c.id AND fi.id = fv.item AND fi.typ = ? AND fv.item = ?
            GROUP BY course_id, shortname
            ORDER BY avgvalue desc";

    if ($courses = $DB->get_records_sql($sql, array($courseitemfiltertyp, $courseitemfilter))) {
        $item = $DB->get_record('feedback_item', array('id'=>$courseitemfilter));
        echo '<h4>'.$item->name.'</h4>';
        echo '<div class="clearfix">';
        echo '<table>';
        echo '<tr><th>Course</th><th>Average</th></tr>';
        $sep_dec = get_string('separator_decimal', 'feedback');
        $sep_thous = get_string('separator_thousand', 'feedback');

        foreach ($courses as $c) {
            $coursecontext = context_course::instance($c->course_id);
            $shortname = format_string($c->shortname, true, array('context' => $coursecontext));

            echo '<tr>';
            echo '<td>'.$shortname.'</td>';
            echo '<td align="right">';
            echo number_format(($c->avgvalue), 2, $sep_dec, $sep_thous);
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>'.get_string('noresults').'</p>';
    }
    echo '<p><a href="analysis_course.php?id=' . $id . '">';
    echo get_string('back');
    echo '</a></p>';
} else {

    echo '<div class="mdl-align">';
    echo '<form name="report" method="get" id="analysis-form">';
    echo html_writer::label(get_string('search_course', 'feedback') . ': ', 'searchcourse');
    echo '<input id="searchcourse" type="text" name="searchcourse" value="'.s($searchcourse).'"/> ';
    echo '<input type="submit" value="'.get_string('search').'"/>';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    $sql = 'select DISTINCT c.id, c.shortname from {course} c, '.
                                          '{feedback_value} fv, {feedback_item} fi '.
                                          'where c.id = fv.course_id and fv.item = fi.id '.
                                          'and fi.feedback = ? '.
                                          'and
                                          ('.$DB->sql_like('c.shortname', '?', false).'
                                          OR '.$DB->sql_like('c.fullname', '?', false).')';
    $params = array($feedback->id, "%$searchcourse%", "%$searchcourse%");

    if ($courses = $DB->get_records_sql_menu($sql, $params)) {
        if (!$courseid) {
            $courses = array('' => get_string('choosedots')) + $courses;
        }
        echo ' '. html_writer::label(get_string('filter_by_course', 'feedback'). ': ', 'coursefilterid');
        echo html_writer::select($courses, 'courseid', $courseid,
                                  null, array('id'=>'coursefilterid', 'class' => 'autosubmit'));

        $PAGE->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => 'coursefilterid', 'nothing' => false))
        );
    }
    if ($courseid) {
        echo ' <a href="analysis_course.php?id=' . $id . '">';
        echo get_string('show_all', 'feedback');
        echo '</a>';
    }

    echo '</form>';
    echo '</div>';
    echo '<hr />';
    $itemnr = 0;
    //print the items in an analysed form
    foreach ($items as $item) {
        if ($item->hasvalue == 0) {
            continue;
        }
        echo '<table>';
        //get the class from item-typ
        $itemobj = feedback_get_item_class($item->typ);
        $itemnr++;
        if ($feedback->autonumbering) {
            $printnr = $itemnr.'.';
        } else {
            $printnr = '';
        }
        $itemobj->print_analysed($item, $printnr, $mygroupid, $courseid);
        if (preg_match('/rated$/i', $item->typ)) {
            $url = new moodle_url('/mod/feedback/analysis_course.php', array('id' => $id,
                'courseitemfilter' => $item->id, 'courseitemfiltertyp' => $item->typ));
            $anker = html_writer::link($url, get_string('sort_by_course', 'feedback'));

            echo '<tr><td colspan="2">'.$anker.'</td></tr>';
        }
        echo '</table>';
    }
}

echo $OUTPUT->footer();

