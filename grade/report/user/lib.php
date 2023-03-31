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
 * Definition of the grade_user_report class is defined
 *
 * @package gradereport_user
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_user\output\myprofile\tree;

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

define("GRADE_REPORT_USER_HIDE_HIDDEN", 0);
define("GRADE_REPORT_USER_HIDE_UNTIL", 1);
define("GRADE_REPORT_USER_SHOW_HIDDEN", 2);

define("GRADE_REPORT_USER_VIEW_SELF", 1);
define("GRADE_REPORT_USER_VIEW_USER", 2);

function grade_report_user_settings_definition(&$mform) {
    global $CFG;

    $options = [
        -1 => get_string('default', 'grades'),
        0 => get_string('hide'),
        1 => get_string('show')
    ];

    if (empty($CFG->grade_report_user_showrank)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showrank', get_string('showrank', 'grades'), $options);
    $mform->addHelpButton('report_user_showrank', 'showrank', 'grades');

    if (empty($CFG->grade_report_user_showpercentage)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showpercentage', get_string('showpercentage', 'grades'), $options);
    $mform->addHelpButton('report_user_showpercentage', 'showpercentage', 'grades');

    if (empty($CFG->grade_report_user_showgrade)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showgrade', get_string('showgrade', 'grades'), $options);

    if (empty($CFG->grade_report_user_showfeedback)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showfeedback', get_string('showfeedback', 'grades'), $options);

    if (empty($CFG->grade_report_user_showweight)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showweight', get_string('showweight', 'grades'), $options);

    if (empty($CFG->grade_report_user_showaverage)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showaverage', get_string('showaverage', 'grades'), $options);
    $mform->addHelpButton('report_user_showaverage', 'showaverage', 'grades');

    if (empty($CFG->grade_report_user_showlettergrade)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showlettergrade', get_string('showlettergrade', 'grades'), $options);
    if (empty($CFG->grade_report_user_showcontributiontocoursetotal)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[$CFG->grade_report_user_showcontributiontocoursetotal]);
    }

    $mform->addElement('select', 'report_user_showcontributiontocoursetotal', get_string('showcontributiontocoursetotal', 'grades'), $options);
    $mform->addHelpButton('report_user_showcontributiontocoursetotal', 'showcontributiontocoursetotal', 'grades');

    if (empty($CFG->grade_report_user_showrange)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showrange', get_string('showrange', 'grades'), $options);

    $options = [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
    ];

    if (!empty($CFG->grade_report_user_rangedecimals)) {
        $options[-1] = $options[$CFG->grade_report_user_rangedecimals];
    }
    $mform->addElement('select', 'report_user_rangedecimals', get_string('rangedecimals', 'grades'), $options);

    $options = [
        -1 => get_string('default', 'grades'),
        0 => get_string('shownohidden', 'grades'),
        1 => get_string('showhiddenuntilonly', 'grades'),
        2 => get_string('showallhidden', 'grades')
    ];

    if (empty($CFG->grade_report_user_showhiddenitems)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[$CFG->grade_report_user_showhiddenitems]);
    }

    $mform->addElement('select', 'report_user_showhiddenitems', get_string('showhiddenitems', 'grades'), $options);
    $mform->addHelpButton('report_user_showhiddenitems', 'showhiddenitems', 'grades');

    $options = [
        -1 => get_string('default', 'grades'),
        GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN => get_string('hide'),
        GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowexhiddenitems', 'grades'),
        GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowinchiddenitems', 'grades')
    ];

    if (empty($CFG->grade_report_user_showtotalsifcontainhidden)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[$CFG->grade_report_user_showtotalsifcontainhidden]);
    }

    $mform->addElement('select', 'report_user_showtotalsifcontainhidden', get_string('hidetotalifhiddenitems', 'grades'), $options);
    $mform->addHelpButton('report_user_showtotalsifcontainhidden', 'hidetotalifhiddenitems', 'grades');

}

/**
 * Profile report callback.
 *
 * @param object $course The course.
 * @param object $user The user.
 * @param boolean $viewasuser True when we are viewing this as the targetted user sees it.
 */
function grade_report_user_profilereport(object $course, object $user, bool $viewasuser = false) {
    if (!empty($course->showgrades)) {

        $context = context_course::instance($course->id);

        // Fetch the return tracking object.
        $gpr = new grade_plugin_return(
            ['type' => 'report', 'plugin' => 'user', 'courseid' => $course->id, 'userid' => $user->id]
        );
        // Create a report instance.
        $report = new gradereport_user\report\user($course->id, $gpr, $context, $user->id, $viewasuser);

        // Print the page.
        // A css fix to share styles with real report page.
        echo '<div class="grade-report-user">';
        if ($report->fill_table()) {
            echo $report->print_table(true);
        }
        echo '</div>';
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param null|stdClass $course Course object
 */
function gradereport_user_myprofile_navigation(tree $tree, stdClass $user, bool $iscurrentuser, ?stdClass $course) {
    if (empty($course)) {
        // We want to display these reports under the site context.
        $course = get_fast_modinfo(SITEID)->get_course();
    }
    $usercontext = context_user::instance($user->id);
    $anyreport = has_capability('moodle/user:viewuseractivitiesreport', $usercontext);

    // Start capability checks.
    if ($anyreport || $iscurrentuser) {
        // Add grade hardcoded grade report if necessary.
        $gradeaccess = false;
        $coursecontext = context_course::instance($course->id);
        if (has_capability('moodle/grade:viewall', $coursecontext)) {
            // Can view all course grades.
            $gradeaccess = true;
        } else if ($course->showgrades) {
            if ($iscurrentuser && has_capability('moodle/grade:view', $coursecontext)) {
                // Can view own grades.
                $gradeaccess = true;
            } else if (has_capability('moodle/grade:viewall', $usercontext)) {
                // Can view grades of this user - parent most probably.
                $gradeaccess = true;
            } else if ($anyreport) {
                // Can view grades of this user - parent most probably.
                $gradeaccess = true;
            }
        }
        if ($gradeaccess) {
            $url = new moodle_url('/course/user.php', array('mode' => 'grade', 'id' => $course->id, 'user' => $user->id));
            $node = new core_user\output\myprofile\node('reports', 'grade', get_string('grades'), null, $url);
            $tree->add_node($node);
        }
    }
}

/**
 * Returns link to user report for the current element
 *
 * @param context_course $context Course context
 * @param int $courseid Course ID
 * @param array  $element An array representing an element in the grade_tree
 * @param grade_plugin_return $gpr A grade_plugin_return object
 * @param string $mode Mode - gradeitem or user
 * @param ?stdClass $templatecontext Template context
 * @return stdClass|null
 */
function gradereport_user_get_report_link(context_course $context, int $courseid, array $element,
        grade_plugin_return $gpr, string $mode, ?stdClass $templatecontext): ?stdClass {
    global $CFG;

    if ($mode == 'user') {
        $reportstring = grade_helper::get_lang_string('userreport_' . $mode, 'gradereport_user');

        if (!isset($templatecontext)) {
            $templatecontext = new stdClass();
        }

        // FIXME: MDL-52678 This get_capability_info is hacky and we should have an API for inserting grade row links instead.
        $canseeuserreport = false;
        if (get_capability_info('gradereport/' . $CFG->grade_profilereport . ':view')) {
            $canseeuserreport = has_capability('gradereport/' . $CFG->grade_profilereport . ':view', $context);
        }

        if ($canseeuserreport) {
            $url = new moodle_url('/grade/report/' . $CFG->grade_profilereport . '/index.php',
                ['userid' => $element['userid'], 'id' => $courseid]);
            $gpr->add_url_params($url);
            $templatecontext->reporturl1 = html_writer::link($url, $reportstring,
                ['class' => 'dropdown-item', 'aria-label' => $reportstring, 'role' => 'menuitem']);
            return $templatecontext;
        }
    }
    return null;
}
