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
 * Displays the learning outcomes page for a course.
 *
 * @package   core_course
 * @copyright 2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$PAGE->set_url('/course/learningoutcomes.php', ['id' => $course->id]);
$PAGE->set_pagelayout('course');
$PAGE->add_body_class('limitedwidth');

require_login($course);
$context = context_course::instance($course->id);

$title = get_string('learningoutcomes', 'core_course');
$PAGE->set_title($course->shortname . ': ' . $title);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

if (empty($CFG->enableoutcomes)) {
    echo $OUTPUT->notification(get_string('outcomesdisabled', 'grades'), \core\output\notification::NOTIFY_INFO);
} else {
    // Build the manage learning outcomes button.
    $manageoutcomesbutton = null;
    if (has_capability('moodle/grade:manageoutcomes', $context)) {
        $manageoutcomeslink = new moodle_url('/grade/edit/outcome/index.php', ['id' => $course->id]);
        $manageoutcomesbutton = new single_button(
            url: $manageoutcomeslink,
            label: get_string('managelearningoutcomes', 'course'),
            method: 'get',
            type: single_button::BUTTON_PRIMARY,
        );
    }

    // Fetch all outcomes.
    $outcomes = grade_outcome::fetch_all_available($course->id);

    if (empty($outcomes)) {
        echo $OUTPUT->notification(get_string('nolearningoutcomesincourse', 'core_course'), \core\output\notification::NOTIFY_INFO);
        // Allow new learning outcomes to be added when there are none.
        if (!empty($manageoutcomesbutton)) {
            echo $OUTPUT->render($manageoutcomesbutton);
        }
    } else {
        $modinfo = get_fast_modinfo($course);
        $format = course_get_format($course);
        $cmitemclass = $format->get_output_classname('content\\section\\cmitem');

        $templatedata = [
            'outcomes' => [],
            'manageoutcomesbutton' => $manageoutcomesbutton ? $manageoutcomesbutton->export_for_template($OUTPUT) : null,
        ];

        foreach ($outcomes as $outcome) {
            $outcomename = $outcome->get_name();
            $outcomedata = [
                'id' => $outcome->id,
                'name' => $outcomename,
                'header' => [
                    'id' => $outcome->id,
                    'num' => $outcome->id,
                    'name' => $outcomename,
                    'title' => $outcomename,
                    'headinglevel' => 3,
                    'headerdisplaymultipage' => false,
                    'displayonesection' => false,
                    'sitehome' => false,
                    'contentcollapsed' => false,
                    'sectionbulk' => false,
                ],
                'summary' => [
                    'summarytext' => $outcome->get_description(),
                ],
                'cmlist' => [
                    'cms' => [],
                    'hascms' => false,
                ],
            ];

            $mappedmodules = grade_outcome::get_modules_mapped_to_course_outcomes($outcome->id, $course->id);

            foreach ($mappedmodules as $index => $cmid) {
                // No matching module.
                if (empty($modinfo->cms[$cmid])) {
                    unset($mappedmodules[$index]);
                    continue;
                }

                $mappedmodules[$index] = $modinfo->cms[$cmid];
            }

            // Sort modules as they appear in the course.
            $modinfo->sort_cm_array($mappedmodules);

            foreach ($mappedmodules as $cm) {
                // Keep visibility logic consistent with course view: teachers/admins with
                // hidden-activity capability can still see restricted items, while students
                // only see items that are on the course page and either available or showing
                // availability information.
                $sectioninfo = $modinfo->get_section_info($cm->sectionnum);
                if (!$sectioninfo->uservisible) {
                    continue;
                }

                // Reuse core displayability logic instead of duplicating the visibility condition.
                $isdisplayable = \core_courseformat\output\local\overview\overviewtable::is_cm_displayable($cm);

                if (!$isdisplayable) {
                    continue;
                }

                $cmitem = new $cmitemclass($format, $sectioninfo, $cm);
                $cmitem = $cmitem->export_for_template($OUTPUT);

                // We are reusing templates from the course view page.
                // There are several items that are not yet compatible with edit mode on this page.
                // Let's unset them so they are not rendered.
                unset($cmitem->cmformat->editing);
                unset($cmitem->cmformat->cmbulk);
                unset($cmitem->cmformat->modavailability);
                unset($cmitem->cmformat->altcontent);
                unset($cmitem->cmformat->afterlink);
                unset($cmitem->cmformat->dates);
                unset($cmitem->cmformat->controlmenu);
                unset($cmitem->cmformat->groupmodeinfo);
                unset($cmitem->cmformat->visibility);
                unset($cmitem->cmformat->completion);
                unset($cmitem->cmformat->modstealth);
                unset($cmitem->cmformat->modhiddenfromstudents);
                 // Unsetting this will remove the ability to edit the activity name.
                unset($cmitem->cmformat->cmname['activityname']['component']);

                $outcomedata['cmlist']['cms'][] = [
                    'cmitem' => $cmitem,
                ];
            }

            if (!empty($outcomedata['cmlist']['cms'])) {
                $outcomedata['cmlist']['hascms'] = true;
            }

            $templatedata['outcomes'][] = $outcomedata;
        }

        echo $OUTPUT->render_from_template('core_course/learningoutcomes', $templatedata);
    }
}

echo $OUTPUT->footer();
