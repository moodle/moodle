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

use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class containing student menu data for intellibard plugin
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */
class student_menu implements renderable, templatable {

    var $params = null;

    public function __construct($params = null) {
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
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;

        require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

        $id = optional_param('id', 0, PARAM_RAW);
        $mentor_role = get_config('local_intelliboard', 't09');
        $def_name = get_string('grades', 'local_intelliboard');
        $intellicart = get_config('local_intelliboard', 'intellicart_student_integration');
        $alt_name = get_config('local_intelliboard', 'grades_alt_text');
        $other_user = optional_param('user', 0, PARAM_INT);
        $show_students = false;
        $grade_name = ($alt_name) ? $alt_name : $def_name;
        $showing_user = $this->params['showinguser'];

        if($intellicart && file_exists($CFG->dirroot . '/local/intellicart/locallib.php')) {
            require_once($CFG->dirroot . '/local/intellicart/locallib.php');
        }

        if ($mentor_role > 0){
            $show_students = intelliboard_instructor_have_access($USER->id);

            if($show_students){
                $students = $DB->get_records_sql(
                    "SELECT u.*
                           FROM {role_assignments} ra
                           JOIN {context} c ON c.id = ra.contextid
                           JOIN {user} u ON u.id = c.instanceid
                          WHERE ra.roleid=:role AND ra.userid = :userid",
                    ['role' => $mentor_role, 'userid' => $USER->id]
                );

                $userslist = [[
                    'id' => 0,
                    'name' => fullname($USER),
                    'isselected' => $other_user == 0,
                    'notselected' => $other_user != 0
                ]];

                foreach($students as $student){
                    $userslist[] = [
                        'id' => $student->id,
                        'name' => fullname($student),
                        'isselected' => $student->id == $other_user,
                        'notselected' => $student->id != $other_user,
                    ];
                }
            }
        }

        $sum_courses = get_user_preferences('enabeled_sum_courses_'.$showing_user->id, '');
        $sum_courses = !empty($sum_courses) ? explode(',', $sum_courses) : [];

        if (!$intellicart) {
            $showseats = false;
            $showwaitlist = false;
            $showsubscriptions = false;
            $intellicartenabled = false;
        } else {
            $intellicartenabled = (
                local_intellicart_enable('', true) &&
                file_exists($CFG->dirroot . '/local/intellicart/locallib.php')
            );
            $showwaitlist = get_config('local_intellicart', 'enablewaitlist');
            $showseats = get_config('local_intellicart', 'enableseatsvendors');
            $showsubscriptions = get_config('local_intellicart', 'enablesubscription');
        }

        $usercoursesdata = enrol_get_users_courses($showing_user->id);
        $usercourses = [];

        foreach ($usercoursesdata as $item) {
            $usercourses[] = [
                'id' => $item->id,
                'fullname' => $item->fullname,
                'checked' =>  empty($sum_courses) || in_array($item->id, $sum_courses)
            ];
        }

        $reportsdata = $this->params['intelliboard']->reports;
        $reports = [];

        foreach ($reportsdata as $key => $report) {
            $reports[] = [
                'reportid' => $key,
                'reportname' => format_string($report->name),
                'reportselected' => $key == $id,
            ];
        }

        return [
            'id' => $id,
            't2' => get_config('local_intelliboard', 't2'),
            't3' => get_config('local_intelliboard', 't3'),
            't4' => get_config('local_intelliboard', 't4'),
            't04' => get_config('local_intelliboard', 't04'),
            't05' => get_config('local_intelliboard', 't05'),
            't06' => get_config('local_intelliboard', 't06'),
            't07' => get_config('local_intelliboard', 't07'),
            't08' => get_config('local_intelliboard', 't08'),
            'messages_url' => (
                new moodle_url('/message/index.php', ['viewing' => 'unread', 'id' => $showing_user->id])
            )->out(),
            'user_picture' => $OUTPUT->user_picture($showing_user, ['size' => 75]),
            'render_students' => $show_students && !empty($students),
            'showing_user_name' => fullname($showing_user),
            'showing_user_email' => format_string($showing_user->email),
            'totals' => intelliboard_learner_totals($showing_user->id),
            'userlist' => isset($userslist) ? $userslist : [],
            'grade_name' => $grade_name,
            'user_courses' => $usercourses,
            'pagehome' => ($PAGE->pagetype == 'home'),
            'pagegrades' => ($PAGE->pagetype == 'grades'),
            'pagebadges' => ($PAGE->pagetype == 'badges'),
            'pagecourses' => ($PAGE->pagetype == 'courses'),
            'pagegreports' => ($PAGE->pagetype == 'reports'),
            'pagemyorders' => ($PAGE->pagetype == 'myorders'),
            'pagemyseats' => ($PAGE->pagetype == 'pagemyseats'),
            'pagemywaitlist' => ($PAGE->pagetype == 'mywaitlist'),
            'pagemysubscriptions' => ($PAGE->pagetype == 'mysubscriptions'),
            'show_reports' => get_config('local_intelliboard', 't48') &&
                isset($this->params['intelliboard']->reports) &&
                !empty($this->params['intelliboard']->reports),
            'reports' => $reports,
            'showseats' => $showseats,
            'showwaitlist' => $showwaitlist,
            'showsubscriptions' => $showsubscriptions,
            'intellicartenabled' => $intellicartenabled,
            'other_user' => optional_param('user', 0, PARAM_INT),
            'pagepath' => $PAGE->url->get_path(),
            'ordersurl' => (new moodle_url('/local/intelliboard/student/orders.php'))->out(),
            'waitlisturl' => (new moodle_url('/local/intelliboard/student/waitlist.php'))->out(),
            'seatsurl' => (new moodle_url('/local/intelliboard/student/seats.php'))->out(),
            'subscriptionurl' => (new moodle_url('/local/intelliboard/student/subscriptions.php'))->out(),
        ];
    }
}
