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

namespace local_intelliboard\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use context_course;
use local_intelliboard\repositories\user_settings;

/**
 * Class containing data of "Instructor dashboard" page
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */
class instructor_index implements renderable, templatable {

    var $params = [];

    public function __construct($params = []) {
        $this->params = $params;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $PAGE, $OUTPUT, $CFG;

        $view = $this->params["view"];
        $course = $this->params["course"];
        $stats = intelliboard_instructor_stats();
        $users = intelliboard_instructor_getcourses('', false, '', true);
        $enrolledusers = get_enrolled_users(context_course::instance($course));

        $stats = (object) [
            "courses" => (int) $stats->courses,
            "grades" => (int) $stats->grades,
            "users" => $users ? $users : (int) $stats->enrolled,
            "enrolled" => $stats->enrolled,
            "grade" => (int) $stats->grade,
            "completed" => (int) $stats->completed,
            "notcompleted" => intval($stats->enrolled) - intval($stats->completed)
        ];

        $settingscourses = array_keys(user_settings::getInstructorDashboardCourses($USER->id));
        $instructorAllCourses = array_values(array_map(function($item) use ($settingscourses) {
            $item->optionselected = in_array($item->id, $settingscourses);
            return $item;
        }, intelliboard_instructor_getcourses('', true, '', false, false)));

        // prepare menu (mode for main cahrt)
        array_walk($this->params["menu"], function(&$item, $index) use ($view, $PAGE) {
            $item = (object) [
                "key" => $index,
                "value" => format_string($item),
                "selected" => $view == $index,
                "url" => $PAGE->url . "&view={$index}",
            ];
        });
        
        $selectedmenu = array_values(array_filter($this->params["menu"], function($item) {
            return $item->selected;
        }));

        $othermenu = array_values(array_filter($this->params["menu"], function($item) {
            return !$item->selected;
        }));

        // prepare courses for course filter
        array_walk($this->params["listofmycourses"], function(&$item, $index) use ($view, $PAGE, $course) {
            $item = (object) [
                "key" => $index,
                "value" => format_string($item),
                "selected" => $course == $index,
                "url" => $PAGE->url . "&view={$view}&course={$index}",
            ];
        });
        
        $selectedmycourse = array_values(array_filter($this->params["listofmycourses"], function($item) {
            return $item->selected;
        }));

        $othermycourses = array_values(array_filter($this->params["listofmycourses"], function($item) {
            return !$item->selected;
        }));

        // prepare enrolled users
        $enrolledusers = array_values(array_map(function($item) {
            return (object) [
                "key" => $item->id,
                "value" => fullname($item),
            ];
        }, $enrolledusers));

        // summary menu
        $summarymenu = [];
        
        if($this->params["pluginsettings"]->n5){
            $summarymenu[] = (object) [
                "key" => "curent_progress",
                "value" => format_string(get_string('in2', 'local_intelliboard'))
            ];
        }
        
        if($this->params["pluginsettings"]->n13){
            $summarymenu[] = (object) [
                "key" => "total_student",
                "value" => format_string(get_string('in27', 'local_intelliboard'))
            ];
        }

        // content class
        if (
            $this->params["pluginsettings"]->n7 || $this->params["pluginsettings"]->n15 ||
            $this->params["pluginsettings"]->n16
        ) {
            $intelliboardbox1class = "50";
        } else {
            $intelliboardbox1class = "100";
        }

        if ($this->params["pluginsettings"]->n6 || $this->params["pluginsettings"]->n14) {
            $intelliboardbox2class = "45";
        } else {
            $intelliboardbox2class = "100";
        }

        // prepare courses
        $courses = array_values(array_map(function($item) {
            return (object) [
                "name" => addslashes(format_string($item->fullname)),
                "data1" => (int)$item->data1,
                "data1perc" => (int)$item->data1 / 100,
                "data2" => (int)$item->data2,
            ];
        }, $this->params["courses"]));

        return [
            "show_content" => !empty($stats->courses),
            "instructorAllCourses" => $instructorAllCourses,
            "instructorheadfull" => !$this->params["pluginsettings"]->n5 && !$this->params["pluginsettings"]->n13,
            "showmainchart" => $this->params["pluginsettings"]->n1 || $this->params["pluginsettings"]->n2 ||
                               $this->params["pluginsettings"]->n3 || $this->params["pluginsettings"]->n12,
            "pluginsettings" => $this->params["pluginsettings"],
            "selectedmenu" => $selectedmenu,
            "othermenu" => $othermenu,
            "iscourseview" => $view == 'course_overview',
            "isgradesview" => $view == 'grades',
            "isactivitiesview" => $view == 'activities',
            "isotherview" => $view != 'course_overview' && $view != 'grades' && $view != 'activities',
            "selectedmycourse" => $selectedmycourse,
            "othermycourses" => $othermycourses,
            "view" => $view,
            "showinstructortotals" => $this->params["pluginsettings"]->n4,
            "showsummarychart" => $this->params["pluginsettings"]->n5 || $this->params["pluginsettings"]->n13,
            "stats" => $stats,
            "summarymenu" => $summarymenu,
            "summarychartlabel" => !$stats->enrolled ? 0 : intval(($stats->completed / $stats->enrolled) * 100),
            "showintelliboardbox1" => $this->params["pluginsettings"]->n6 || $this->params["pluginsettings"]->n14 ||
                                     $this->params["pluginsettings"]->n18,
            "showintelliboardbox2" => $this->params["pluginsettings"]->n7 || $this->params["pluginsettings"]->n15 ||
                                     $this->params["pluginsettings"]->n16,
            "intelliboardbox1class" => $intelliboardbox1class,
            "intelliboardbox2class" => $intelliboardbox2class,
            "enrolledusers" => $enrolledusers,
            "currentlanguage" => current_language(),
            "timestartdate" => $this->params["timestartdate"],
            "timefinishdate" => $this->params["timefinishdate"],
            "dateformat" => intelli_date_format(),
            "jsstrings" => $this->get_js_strings(),
            "nodatabox" => $OUTPUT->box(get_string('no_data', 'local_intelliboard'), 'generalbox alert'),
            "chart7title" => intellitext(get_string('grade', 'local_intelliboard')) .
                             !$this->params["pluginsettings"]->raw ?'(' . intellitext(get_string("scale_percentage", "local_intelliboard")) . ')':'',
            "factorInfo" => chart_options(),
            "learningprogressoptions" => format_string(chart_options()->LearningProgressCalculation),
            "correlationsoptions" => format_string(chart_options()->CorrelationsCalculation),
            "gradeactivitiesoverviewoptions" => format_string(chart_options()->GradeActivitiesOverview),
            "gradeprogressionoptions" => format_string(chart_options()->GradeProgression),
            "courses" => $courses,
            "course" => $course,
            "wwwroot" => $CFG->wwwroot,
            "pageurl" => $PAGE->url,
            "totara_version" => isset($CFG->totara_version) ? $CFG->totara_version : null,
        ];
    }

    private function get_js_strings() {
        return (object) [
            "grade" => intellitext(get_string('grade', 'local_intelliboard')),
            "in13" => intellitext(get_string('in13', 'local_intelliboard')),
            "in14" => intellitext(get_string('in14', 'local_intelliboard')),
            "in15" => intellitext(get_string('in15', 'local_intelliboard')),
            "in19" => intellitext(get_string('in19', 'local_intelliboard')),
            "in25" => intellitext(get_string('in25', 'local_intelliboard')),
            "in27" => intellitext(get_string('in27', 'local_intelliboard')),
            "in29" => intellitext(get_string('in29', 'local_intelliboard')),
            "in30" => intellitext(get_string('in30', 'local_intelliboard')),
            "s25" => intellitext(get_string('s25', 'local_intelliboard')),
            "s45" => intellitext(get_string('s45', 'local_intelliboard')),
            "s46" => intellitext(get_string('s46', 'local_intelliboard')),
            "s47" => intellitext(get_string('s47', 'local_intelliboard')),
            "s48" => intellitext(get_string('s48', 'local_intelliboard')),
            "completed" => intellitext(get_string('completed', 'local_intelliboard')),
            "incompleted" => intellitext(get_string('incomplete', 'local_intelliboard')),
            "learners" => intellitext(get_string('learners', 'local_intelliboard')),
            "courses" => intellitext(get_string('courses')),
            "course" => intellitext(get_string('course')),
            "loading" => intellitext(get_string('loading', 'local_intelliboard')),
            "enrolled" => intellitext(get_string('enrolled', 'local_intelliboard')),
            "gradeactivitiesoverview" => intellitext(get_string('grade_activities_overview', 'local_intelliboard')),
            "total" => intellitext(get_string('total', 'local_intelliboard')),
            "selectuser" => intellitext(get_string('select_user', 'local_intelliboard')),
            "nodata" => intellitext(get_string('no_data','local_intelliboard')),
        ];
    }
}
