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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\output\tables;

use DateTime;
use html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/local/intelliboard/locallib.php');

class student_badges extends \table_sql {
    public function __construct($uniqueid) {
        global $PAGE, $USER, $CFG;

        parent::__construct($uniqueid);

        $headers = [
            get_string('badges'),
            get_string('issuer', 'local_intelliboard'),
            get_string('criteria_method', 'local_intelliboard'),
            get_string('courses', 'local_intelliboard'),
            get_string('progress', 'local_intelliboard'),
        ];
        $columns = ['badge', 'issuer', 'criteria_method', 'courses', 'progress'];

        $this->define_headers($headers);
        $this->define_columns($columns);

        $criteriaconcat = get_operator(
            'GROUP_CONCAT', "CONCAT(bcrp.name, '|', bcrp.value)", ['separator' => ', '])
        ;
        $coursestatsconcat = get_operator(
            'GROUP_CONCAT',
            "CONCAT(COALESCE(c.id, -1), '|', COALESCE(c.fullname, 'null') ,'|', COALESCE(cc.timecompleted, -1), '|', COALESCE(gg.finalgrade, -1))",
            ['separator' => ', ']
        );

        $fields = "bdg.id,
                   bdg.name,
                   bdg.issuername,
                   bdgc.method AS criteria_method,
                   {$criteriaconcat} AS criteria,
                   {$coursestatsconcat} AS course_stats";
        $from = "{badge} bdg
            JOIN {badge_criteria} bdgc ON bdgc.badgeid = bdg.id AND bdgc.criteriatype IN (4, 5)
            JOIN {badge_criteria_param} bcrp ON bcrp.critid = bdgc.id
       LEFT JOIN {course} c ON c.id = (CASE WHEN bcrp.name LIKE 'course_%' THEN " . (in_array($CFG->dbtype, ['mysqli', 'mariadb']) ? 'bcrp.value' : 'bcrp.value::INTEGER') . " ELSE -1 END)
            JOIN (SELECT DISTINCT courseid
                    FROM {enrol} e
                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                   WHERE ue.userid = :userid3
            ) ec ON ec.courseid = c.id
       LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
       LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = :userid1
       LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = :userid2";
        $where = "bdg.status IN(1,3) GROUP BY bdg.id, bdg.name, bdg.issuername, bdgc.method";
        $params = ['userid1' => $USER->id, 'userid2' => $USER->id, 'userid3' => $USER->id];

        $this->set_count_sql(
            "SELECT COUNT(bdg.id)
               FROM {badge} bdg
               JOIN {badge_criteria} bdgc ON bdgc.badgeid = bdg.id AND bdgc.criteriatype IN (4, 5)"
        );

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
    }

    public function col_badge($row) {
        return $row->name;
    }

    public function col_issuer($row) {
        return $row->issuername;
    }

    public function col_criteria_method($row) {
        if($row->criteria_method == 1) {
            return get_string('criteria_all_courses', 'local_intelliboard');
        } else if($row->criteria_method == 2) {
            return get_string('criteria_any_course', 'local_intelliboard');
        }

        return '';
    }

    public function col_courses($row) {
        return $row->courses;
    }

    public function col_progress($row) {
        $html = html_writer::start_tag("div", ["class" => "grade"]);
        $html .= html_writer::tag(
            "div", "", ["class" => "circle-progress", "data-percent" => format_float($row->completion_percent)]
        );
        $html .= html_writer::end_tag("div");

        return $html;
    }

    public function build_table() {
        global $OUTPUT;

        if ($this->rawdata instanceof \Traversable && !$this->rawdata->valid()) {
            return;
        }
        if (!$this->rawdata) {
            return;
        }

        foreach ($this->rawdata as $row) {
            $criteria = array_map(function ($item) {
                $item = explode('|', $item);
                return (object) [
                    'name' => $item[0],
                    'value' => format_float(floatval($item[1])),
                ];
            }, explode(', ', $row->criteria));

            $courses = [];
            foreach (explode(', ', $row->course_stats) as $item) {
                $item = explode('|', $item);

                if($item[0] != -1) {
                    $courses[intval($item[0])] = (object) [
                        'id' => intval($item[0]),
                        'fullname' => $item[1],
                        'completed' => $item[2] == '-1' ? null : intval($item[2]),
                        'grade' => $item[3] == '-1' ? null : format_float(floatval($item[3])),
                    ];
                }
            }

            /** @var array $renderdata Data for rendering column "courses" */
            $renderdata = [];
            $completedcriteria = 0;
            /** Check badge courses */
            foreach($courses as $course) {
                $coursecriteriacompleted = true;
                $renderdatarow = [
                    'name' => $course->fullname,
                ];

                /** Check course criteria */
                foreach($criteria as $criterion) {
                    if($criterion->name == "course_{$course->id}") {
                        $renderdatarow['completion'] = ($course->completed !== null) ? get_string('yes') : get_string('no');
                        $renderdatarow['completionclass'] = ($course->completed !== null) ? 'good' : 'bad';

                        if($course->completed === null) {
                            $coursecriteriacompleted = false;
                        }
                    }

                    if($criterion->name == "grade_{$course->id}") {
                        $renderdatarow['grade'] = $course->grade === null ? '-' : $course->grade;
                        $renderdatarow['requiredgrade'] = $criterion->value;
                        $renderdatarow['gradeclass'] = $course->grade < $criterion->value ? 'bad' : 'good';

                        if($course->grade === null) {
                            $coursecriteriacompleted = false;
                        } elseif ($course->grade < $criterion->value) {
                            $coursecriteriacompleted = false;
                        }
                    }

                    if($criterion->name == "bydate_{$course->id}"){
                        $renderdatarow['bydate'] = !$course->completed ? '-' : userdate(
                            $course->completed, '%d-%m-%Y'
                        );
                        $renderdatarow['requiredbydate'] = userdate($criterion->value, '%d-%m-%Y');

                        if($course->completed === null) {
                            $coursecriteriacompleted = false;
                            $renderdatarow['bydateclass'] = 'bad';
                        } else {
                            $date = new DateTime();
                            $date->setTimestamp($course->completed);
                            $date->setTime(0,0,0);

                            if($date->getTimestamp() > $criterion->value) {
                                $renderdatarow['bydateclass'] = 'bad';
                                $coursecriteriacompleted = false;
                            } else {
                                $renderdatarow['bydateclass'] = 'good';
                            }
                        }
                    }
                }

                $renderdata[] = $renderdatarow;
                $completedcriteria += $coursecriteriacompleted ? 1 : 0;
            }

            if($row->criteria_method == 1) {
                $totalcourse = count($courses);
                $percent = $totalcourse ? (($completedcriteria / $totalcourse) * 100) : 0;
            } else if($row->criteria_method == 2) {
                $percent = $completedcriteria > 0 ? 100 : 0;
            }

            $prepearedRow['id'] = $row->id;
            $prepearedRow['name'] = $row->name;
            $prepearedRow['issuername'] = $row->issuername;
            $prepearedRow['courses'] = $OUTPUT->render_from_template(
                'local_intelliboard/student_badges_courses_field', ['courses' => $renderdata]
            );
            $prepearedRow['criteria_method'] = $row->criteria_method;
            $prepearedRow['completion_percent'] = $percent;

            $formattedrow = $this->format_row($prepearedRow);
            $this->add_data_keyed($formattedrow,
                $this->get_row_class($prepearedRow));
        }
    }
}
