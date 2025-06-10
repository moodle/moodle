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
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

require_once($CFG->dirroot . '/blocks/cps/formslib.php');

interface team_states {
    const QUERY = 'query';
    const REQUEST = 'request';
    const REVIEW = 'review';
    const MANAGE = 'manage';
    const SECTIONS = 'sections';
}

abstract class team_request_form extends cps_form implements team_states {
}

class team_request_form_select extends team_request_form {
    public $current = self::SELECT;
    public $next = self::SHELLS;

    public static function build($semesters) {
        return array('semesters' => $semesters);
    }

    public function definition() {
        $m =& $this->_form;

        $semesters = $this->_customdata['semesters'];

        $m->addElement('header', 'select_course', self::_s('select'));

        foreach ($semesters as $semester) {
            foreach ($semester->courses as $course) {

                $display = $this->display_course($course, $semester);

                if (cps_team_request::exists($course, $semester)) {
                    $display .= ' (' . self::_s('team_request_option') . ')';
                }

                $key = $semester->id . '_' . $course->id;

                $m->addElement('radio', 'selected', '', $display, $key);
            }
        }

        $m->addRule('selected', self::_s('err_select_one'), 'required', null, 'client');

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        if (empty($data['selected'])) {
            return array('selected' => self::_s('err_select_one'));
        }

        list($semid, $couid) = explode('_', $data['selected']);
        $semesters = $this->_customdata['semesters'];

        $semester = $semesters[$semid];
        $course = $semester->courses[$couid];

        if (cps_team_request::exists($course, $semester)) {
            $this->next = self::UPDATE;
        }

        return true;
    }
}

class team_request_form_update extends team_request_form {
    public $current = self::UPDATE;
    public $next = self::MANAGE;
    public $prev = self::SELECT;

    const ADD_USER_CURRENT = 1;
    const ADD_COURSE = 2;
    const MANAGE_REQUESTS = 3;
    const MANAGE_SECTIONS = 4;

    public static function build($courses) {
        $reshell = optional_param('reshell', 0, PARAM_INT);

        $shells = optional_param('shells', null, PARAM_INT);

        $extra = $shells ? array('shells' => $shells - $reshell) : array();

        return $extra + team_request_form_shells::build($courses);
    }

    /**
     * Gets the largest grouping id for a particular course in the
     * enrol_cps_team_sections table, excluding any records for the current
     * request.  This should be the grouping id used in the most recently
     * _completed_ team teach. If there are are no grouping ids for that
     * course, it should return 0.
     *
     * Note: There is a small risk of interference if two users request
     * team-teaching at the same time.  We should really have a mutex lock on
     * enrol_cps_team_sections during this method and until the value obtained
     * is used to insert a record into this table.
     *
     * @param $coursid     The UES course id for this course.  Note that this
     *                     is a UES course id which has a 1-1 relationship to
     *                     catalog courses, not the Moodle course id which is
     *                     different for different instructor/course
     *                     combinations.
     * @param $requestid   The id for the request currently being made.
     * @return             The number of the latest group for this course,
     *                     excluding the group number of the current request,
     *                     or 0 if no other group exists for this course.
     */
    private function get_latest_group($courseid, $requestid) {
        global $DB;
        $resultfieldset = $DB->get_records_sql("SELECT `groupingid` " .
                                               "FROM {enrol_cps_team_sections} " .
                                               "WHERE `courseid` = '$courseid' " .
                                                    "AND `requestid` != '$requestid' " .
                                               "ORDER BY `groupingid` DESC LIMIT 1"
                                             , null
                                             , IGNORE_MISSING
        );
        if (!$resultfieldset) {
            return 0;
        } else {
            return (array_slice($resultfieldset, 0, 1)[0])->groupingid;
        }
    }

    public function definition() {
        $m =& $this->_form;

        $course = $this->_customdata['selected_course'];

        $semester = $this->_customdata['semester'];

        $todisplay = $this->to_display($semester);

        $m->addElement('header', 'selected_course', $todisplay($course));

        $m->addElement('static', 'all_requests', self::_s('team_following'), '');

        $teamteaches = cps_team_request::in_course($course, $semester);

        $ismaster = false;
        $isotheruser = false;
        $anyapproved = false;
        $selfapproved = false;

        $selectedusers = array();
        $queries = array();

        $groupingmap = array();
        $firstrequest = array_slice($teamteaches, 0, 1)[0];
        $groupingid = $this->get_latest_group($firstrequest->requested_course, $firstrequest->requested);
        foreach ($teamteaches as $request) {

            if ($request->is_owner()) {
                $ismaster = true;

                $user = $request->other_user();

                $othercourse = $request->other_course();
                if (!isset($groupingmap[$othercourse->id])) {
                    $groupingid++;
                    $groupingmap[$othercourse->id] = $groupingid;
                }

                $queries[$groupingid] = array(
                    'department' => $othercourse->department,
                    'cou_number' => $othercourse->cou_number
                );

                $selectedusers[$groupingid][] = $user->id;
            } else if ($request->is_other_user()) {
                $isotheruser = true;
            }

            // If the current user is involved in the request, add a label for it to the form.
            if ($ismaster || $isotheruser) {
                if ($request->approved()) {
                    $anyapproved = true;
                    $append = self::_s('team_approved');
                    if ($request->is_other_user()) {
                        $selfapproved = true;
                    }
                } else {
                    $append = self::_s('team_not_approved');
                }
                
                $label = $request->label() . ' - ' . $append;

                $m->addElement('static', 'selected_' . $request->id, '', $label);
            }


        }

        $m->addElement('static', 'breather', '', '');

        if ($ismaster) {
            $m->addElement('radio', 'update_option', '',
                self::_s('team_current'), self::ADD_USER_CURRENT);

            $m->addElement('radio', 'update_option', '',
                self::_s('team_add_course'), self::ADD_COURSE);
        }

        $limit = get_config('block_cps', 'team_request_limit');
        $shellsrange = range(1, $limit - $groupingid);

        $options = array_combine($shellsrange, $shellsrange);

        $m->addElement('select', 'reshell', self::_s('team_reshell'), $options);

        $m->setDefault('reshell', 1);

        $m->disabledIf('reshell', 'update_option', 'neq', self::ADD_COURSE);

        $m->addElement('radio', 'update_option', '',
            self::_s('team_manage_requests'), self::MANAGE_REQUESTS);

        if ($selfapproved || ($ismaster && $anyapproved)) {
            global $OUTPUT;

            $icon = $OUTPUT->help_icon('team_manage_sections', 'block_cps');

            $m->addElement('radio', 'update_option', '',
                self::_s('team_manage_sections') . $icon, self::MANAGE_SECTIONS);
        }

        $m->setDefault('update_option', self::MANAGE_REQUESTS);
        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'shells', $groupingid);
        $m->setType('shells', PARAM_INT);

        foreach ($queries as $number => $query) {
            $users = implode(',', $selectedusers[$number]);

            $m->addElement('hidden', 'selected_users' . $number . '_str', $users);
            $m->setType('selected_users' . $number . '_str', PARAM_TEXT);

            foreach ($query as $key => $value) {
                $m->addElement('hidden', 'query' . $number . '[' . $key . ']', $value);
                switch($key){
                    case 'department':
                        $ptype = PARAM_ALPHANUMEXT;
                        break;
                    case 'cou_number':
                        $ptype = PARAM_ALPHANUM;
                        break;
                    default:
                        $ptype = PARAM_INT;
                        break;
                }

                $m->setType('query' . $number . '[' . $key . ']', $ptype);
            }
        }

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        if (isset($data['back'])) {
            return true;
        }

        switch ($data['update_option']) {
            case self::ADD_USER_CURRENT:
                $this->next = self::REQUEST;
                break;
            case self::ADD_COURSE:
                $this->next = self::QUERY;
                break;
            case self::MANAGE_SECTIONS:
                $this->next = self::SECTIONS;
                break;
        }

        return true;
    }
}

class team_request_form_manage extends team_request_form {
    public $current = self::MANAGE;
    public $prev = self::UPDATE;
    public $next = self::CONFIRM;

    const NOTHING = 0;
    const APPROVE = 1;
    const REVOKE = 2;

    public static function build($courses) {
        return team_request_form_shells::build($courses);
    }

    public function definition() {
        $m =& $this->_form;

        $course = $this->_customdata['selected_course'];

        $semester = $this->_customdata['semester'];

        $todisplay = $this->to_display($semester);

        $tobold = function ($text) {
            return "<strong>$text</strong>";
        };

        $filler = function ($howmuch) {
            $spaces = range(1, $howmuch);

            return implode('', array_map(function ($sp) {
                return '&nbsp;';
            }, $spaces));
        };

        $m->addElement('header', 'selected_course', $todisplay($course));

        $m->addElement('static', 'team_error', '', '');

        $m->addElement('static', 'action_labels', '',
            $tobold(self::_s('team_actions')) . $filler(50) .
            $tobold(self::_s('team_requested_courses')));

        $teamteaches = cps_team_request::in_course($course, $semester);

        $mastersoptionadded = false;

        foreach ($teamteaches as $request) {
            // The current user is the master of this request.
            $master = $request->is_owner();
            // The current user is the other user of this request.
            $otheruser = $request->is_other_user();

            // If the current user is involved in this request, add a set of
            // radio buttons to the form for this request.
            if ($otheruser || $master) {
                $approval = $request->approved() ?
                    self::_s('team_approved') :
                    self::_s('team_not_approved');

                $label = $request->label() . ' - <strong>' . $approval . '</strong>';

                $options = array (
                    $m->createELement('radio', 'approval_' . $request->id, '',
                        self::_s('team_do_nothing'), self::NOTHING)
                );

                if ($otheruser and !$request->approved()) {
                    $options[] =
                        $m->createELement('radio', 'approval_' . $request->id, '',
                            self::_s('team_approve'), self::APPROVE);
                }

                if ($master) {
                    $verbiage = self::_s('team_revoke');
                } else if ($request->approved()) {
                    $verbiage = self::_s('team_cancel');
                } else {
                    $verbiage = self::_s('team_deny');
                }

                $options[] =
                    $m->createELement('radio', 'approval_' . $request->id, '',
                        $verbiage, self::REVOKE);

                $options[] =
                    $m->createElement('static', 'request' . $request->id, '', $label);

                $m->addGroup($options, 'options_' . $request->id, '&nbsp;',
                    $filler(3), true);
                
            }
        }

        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'update_option', '');
        $m->setType('update_option', PARAM_INT);

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {

        if (isset($data['back'])) {
            return true;
        }

        $selected = 0;
        $course = $this->_customdata['selected_course'];
        $semester = $this->_customdata['semester'];
        $teams = cps_team_request::in_course($course, $semester);

        foreach ($teams as $id => $team) {
            $approval = $data['options_' . $id]['approval_' . $id];

            if ($approval != self::NOTHING) {
                $selected ++;
            }
        }

        if (empty($selected)) {
            return array('team_error' => self::_s('err_manage_one'));
        }

        return true;
    }
}

class team_request_form_confirm extends team_request_form {
    public $current = self::CONFIRM;
    public $next = self::FINISHED;
    public $prev = self::MANAGE;

    public static function build($courses) {
        return team_request_form_shells::build($courses);
    }

    public function definition() {
        $m =& $this->_form;
        $course = $this->_customdata['selected_course'];
        $semester = $this->_customdata['semester'];
        $todisplay = $this->to_display($semester);
        $teamteaches = cps_team_request::in_course($course, $semester);
        $m->addElement('header', 'selected_course', $todisplay($course));
        $approved = array();
        $denied = array();

        foreach ($teamteaches as $id => $request) {
            $m->addElement('hidden', 'options_' . $id . '[approval_' . $id . ']', '');
            $m->setType('options_' . $id . '[approval_' . $id . ']', PARAM_INT);

            if (!isset($this->_customdata['options_' . $id])) {
                continue;
            }

            $action = $this->_customdata['options_' . $id]['approval_' . $id];

            // TODO rewrite case statement so that it will not throw a warning in PHP 7.3 10/11/2019.
            switch ($action) {
                case team_request_form_manage::APPROVE:
                    $approved[] = $request;
                    break;
                case team_request_form_manage::REVOKE:
                    $denied[] = $request;
                case team_request_form_manage::NOTHING:
                    // Old: continue;.
                    break;
            }
        }

        if ($approved) {
            $m->addElement('static', 'approved', self::_s('team_to_approve'), '');

            foreach ($approved as $request) {
                $m->addElement('static', 'approve_' . $request->id, '', $request->label());
            }
        }

        if ($denied) {
            $m->addElement('static', 'not_approved', self::_s('team_to_revoke'), '');

            foreach ($denied as $request) {
                $m->addElement('static', 'deny_' . $request->id, '', $request->label());
            }
        }

        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'update_option', '');
        $m->setType('update_option', PARAM_INT);

        $this->generate_states_and_buttons();
    }
}

/**
 * Second form in the team-teach request wizard
 */
class team_request_form_shells extends team_request_form {
    public $current = self::SHELLS;
    public $prev = self::SELECT;
    public $next = self::QUERY;

    public static function build($semesters) {
        $selected = required_param('selected', PARAM_RAW);

        list($semid, $couid) = explode('_', $selected);

        $semester = $semesters[$semid];
        $course = $semester->courses[$couid];

        return array('selected_course' => $course, 'semester' => $semester);
    }

    public function definition() {
        $m =& $this->_form;

        $course = $this->_customdata['selected_course'];
        $sem = $this->_customdata['semester'];
        $display = $this->display_course($course, $sem);

        $m->addElement('header', 'selected_course', $display);

        $threshold = get_config('block_cps', 'team_request_limit');
        $range = range(1, $threshold);
        $options = array_combine($range, $range);

        $m->addElement('select', 'shells', self::_s('team_how_many'), $options);
        $m->addHelpButton('shells', 'team_how_many', 'block_cps');
        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);

        $this->generate_states_and_buttons();
    }
}

class team_request_form_query extends team_request_form {
    public $current = self::QUERY;
    public $prev = self::SHELLS;
    public $next = self::REQUEST;

    public static function build($courses) {
        $shells = required_param('shells', PARAM_INT);

        $reshell = optional_param('reshell', 0, PARAM_INT);

        // Don't need to dup this add.
        $current = required_param('current', PARAM_TEXT);

        $toadd = ($reshell and $current == self::UPDATE);

        $extra = array(
            'shells' => $toadd ? $shells + $reshell : $shells,
            'reshell' => $reshell
        );

        return $extra + team_request_form_shells::build($courses);
    }

    public function definition() {
        $m =& $this->_form;

        $course = $this->_customdata['selected_course'];

        $semester = $this->_customdata['semester'];

        $shells = $this->_customdata['shells'];

        $updateoption = optional_param('update_option', null, PARAM_INT);

        if ($updateoption) {
            $m->addElement('hidden', 'update_option', $updateoption);
            $m->setType('update_option', PARAM_INT);
            $this->prev = self::UPDATE;
        }

        $display = $this->display_course($course, $semester);

        $m->addElement('header', 'selected_course', $display);

        $m->addElement('static', 'err_label', '', '');

        $tobold = function ($s) {
            return "<strong>$s</strong>";
        };

        $fill = function ($n) {
            $spaces = range(1, $n);
            return array(implode('', array_map(function ($d) {
                return '&nbsp;';
            },
            $spaces)));
        };

        $dept = self::_s('department');
        $cou = self::_s('cou_number');

        $labels = array(
            $m->createELement('static', 'dept_label', '', $tobold($dept)),
            $m->createELement('static', 'cou_label', '', $tobold($cou))
        );

        $m->addGroup($labels, 'query_labels', '&nbsp;', $fill(23), false);

        foreach (range(1, $shells) as $number) {
            $texts = array(
                $m->createELement('text', 'department', ''),
                $m->createELement('text', 'cou_number', '')
            );

            $display = self::_s('team_query_for', $semester);

            $group = $m->addGroup($texts, 'query' . $number, $display, $fill(1), true);

            $m->setType($group->getElementName('department'), PARAM_ALPHANUMEXT);
            $m->setType($group->getElementName('cou_number'), PARAM_ALPHANUM);
            $m->addElement('hidden', 'selected_users' . $number . '_str', '');
            $m->setType('selected_users' . $number . '_str', PARAM_TEXT);
        }

        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_TEXT);
        $m->addElement('hidden', 'shells', '');
        $m->setType('shells', PARAM_INT);
        $m->addElement('hidden', 'reshell', 0);
        $m->setType('reshell', PARAM_INT);

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        global $USER;

        if (isset($data['back'])) {
            return true;
        }

        $oneorother = function ($one, $other) {
            return ($one and !$other) or (!$one and $other);
        };

        $errors = array();

        $semester = $this->_customdata['semester'];

        $valid = false;
        foreach (range(1, $data['shells']) as $number) {
            $query = $data['query' . $number];

            if (empty($query['department']) and empty($query['cou_number'])) {
                continue;
            }

            if ($oneorother($query['department'], $query['cou_number'])) {
                $errors['err_label'] = self::_s('err_team_query');
                return $errors;
            }

            // Do any sections exists?
            $course = ues_course::get($query);
            $a = (object) $query;

            if (empty($course)) {
                $errors['err_label'] = self::_s('err_team_query_course', $a);
                return $errors;
            }

            $sections = $course->sections($semester);

            if (empty($sections)) {
                $a->year = $semester->year;
                $a->name = $semester->name;

                $errors['err_label'] = self::_s('err_team_query_sections', $a);
                return $errors;
            }

            $valid = true;
        }

        if (!$valid) {
            $errors['err_label'] = self::_s('err_team_query');
        }

        return $errors;
    }
}

class team_request_form_request extends team_request_form {
    public $current = self::REQUEST;
    public $prev = self::QUERY;
    public $next = self::REVIEW;

    public static function build($courses) {
        $data = team_request_form_query::build($courses);

        $queries = array();
        $currentselected = array();

        foreach (range(1, $data['shells']) as $number) {
            $key = 'query' . $number;

            $query = required_param_array($key, PARAM_RAW);

            $queries[$key] = $query;

            $users = optional_param('selected_users' . $number . '_str', null,
                PARAM_TEXT);

            if ($users) {
                $queries['selected_users' . $number] = explode(',', $users);
            }
        }

        return $queries + team_request_form_query::build($courses);
    }

    public function definition() {
        global $USER;

        $m =& $this->_form;

        $selectedcourse = $this->_customdata['selected_course'];
        $semester = $this->_customdata['semester'];
        $updateoption = optional_param('update_option', null, PARAM_INT);

        if ($updateoption) {
            $m->addElement('hidden', 'update_option', $updateoption);
            $m->setType('update_option', PARAM_INT);
            $addinguser = team_request_form_update::ADD_USER_CURRENT;

            $this->prev = $updateoption == $addinguser ? self::UPDATE :
                $this->prev;
        }

        $todisplay = $this->to_display($semester);

        $m->addElement('header', 'selected_course', $todisplay($selectedcourse));

        foreach (range(1, $this->_customdata['shells']) as $number) {
            $users = array();
            $key = 'query' . $number;
            $query = $this->_customdata[$key];

            $m->addElement('hidden', 'query' . $number . '[department]', '');
            $m->setType('query' . $number . '[department]', PARAM_ALPHANUMEXT);
            $m->addElement('hidden', 'query' . $number . '[cou_number]', '');
            $m->setType('query' . $number . '[cou_number]', PARAM_ALPHANUM);

            if (empty($query['department'])) {
                $m->addElement('hidden', 'selected_users' . $number, '');
                $m->setType('selected_users' . $number, PARAM_INT);
                continue;
            }

            $othercourse = ues_course::get(array(
                'department' => $query['department'],
                'cou_number' => $query['cou_number']
            ));

            $othersections = $othercourse->sections($semester);

            // Should query allow lookup to non-primaries?
            $otherteachers = $othercourse->teachers($semester);

            foreach ($otherteachers as $teacher) {
                if ($teacher->userid == $USER->id) {
                    continue;
                }

                $user = $teacher->user();
                $sectioninfo = $othersections[$teacher->sectionid];
                $display = fullname($user) . " ($sectioninfo,...)";
                $users[$teacher->userid] = $display;
            }

            $m->addElement('static', 'query' . $number . '_course', $todisplay($othercourse));

            $select =& $m->addElement('select', 'selected_users' . $number,
                self::_s('team_teachers'), $users);

            $m->setType('selected_users' . $number, PARAM_INT);

            $select->setMultiple(true);

            $m->addHelpButton('selected_users' . $number, 'team_teachers', 'block_cps');
            $m->addElement('hidden', 'selected_users' . $number . '_str', '');
            $m->setType('selected_users' . $number . '_str', PARAM_RAW);
        }

        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'shells', '');
        $m->setType('shells', PARAM_INT);
        $m->addElement('hidden', 'reshell', 0);
        $m->setType('reshell', PARAM_INT);

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        if (isset($data['back'])) {
            return true;
        }

        $errors = array();

        $shells = $data['shells'];

        foreach (range(1, $shells) as $number) {
            $key = 'selected_users' . $number;

            if (!isset($data[$key])) {
                $errors[$key] = self::_s('err_select_teacher');
            }
        }

        return $errors;
    }
}

class team_request_form_review extends team_request_form {
    public $current = self::REVIEW;
    public $prev = self::REQUEST;
    public $next = self::FINISHED;

    public static function build($courses) {
        $data = team_request_form_request::build($courses);

        $usersdata = array();

        foreach (range(1, $data['shells']) as $number) {
            $key = 'selected_users' . $number;

            $users = optional_param_array($key, null, PARAM_INT);
            $userids = optional_param($key . '_str', null, PARAM_TEXT);
            $userid = ($users) ? implode(',', $users) : $userids;
            $usersdata[$key . '_str'] = $userid;
        }
        return $usersdata + $data;
    }

    public function definition() {
        $m =& $this->_form;
        $course = $this->_customdata['selected_course'];
        $semester = $this->_customdata['semester'];
        $todisplay = $this->to_display($semester);

        $m->addElement('header', 'review', self::_s('review_selection'));
        $m->addElement('static', 'selected_course', $todisplay($course), self::_s('team_with'));

        foreach (range(1, $this->_customdata['shells']) as $number) {
            $query = (object) $this->_customdata['query' . $number];

            $m->addElement('hidden', 'query' . $number . '[department]', '');
            $m->setType('query' . $number . '[department]', PARAM_ALPHANUMEXT);
            $m->addElement('hidden', 'query' . $number . '[cou_number]', '');
            $m->setType('query' . $number . '[cou_number]', PARAM_ALPHANUM);

            if (empty($query->department)) {
                $m->addElement('hidden', 'selected_users' . $number . '_str', '');
                continue;
            }

            $userids = $this->_customdata['selected_users' . $number . '_str'];
            $users = ues_user::get_all(ues::where('id')->in(explode(",", $userids)));

            foreach ($users as $user) {
                $str = $todisplay($query) . ' with ' . fullname($user);

                $m->addElement('static', 'selected_user_' . $user->id, '', $str);
            }

            $m->addElement('hidden', 'selected_users' . $number . '_str', $userids);
            $m->setType('selected_users' . $number . '_str', PARAM_RAW);
        }

        $m->addElement('static', 'breather', '', '');
        $m->addElement('static', 'please_note', self::_s('team_note'), self::_s('team_going_email'));
        $m->addElement('hidden', 'selected', '');
        $m->setType('selected', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'shells', '');
        $m->setType('shells', PARAM_INT);
        $m->addElement('hidden', 'reshell', 0);
        $m->setType('reshell', PARAM_INT);

        $updateoption = optional_param('update_option', null, PARAM_INT);
        if ($updateoption) {
            $m->addElement('hidden', 'update_option', $updateoption);
            $m->setType('update_option', PARAM_INT);
        }

        $this->generate_states_and_buttons();
    }
}

class team_request_form_finish implements finalized_form {
    public function process($data, $semesters) {
        list($semid, $couid) = explode('_', $data->selected);

        $semester = $semesters[$semid];
        $course = $semester->courses[$couid];

        $teamteaches = cps_team_request::in_course($course, $semester);

        $exists = !empty($data->update_option);

        if ($exists and $data->update_option == team_request_form_update::MANAGE_REQUESTS) {
            $this->handle_approvals($data, $teamteaches);
        } else {
            $this->save_or_update($data, $teamteaches, $couid, $semid);
        }
    }

    public function handle_approvals($data, $teamteaches) {
        $toundo = array();

        foreach ($teamteaches as $id => $teamteach) {
            $action = $data->{'options_' . $id}['approval_' . $id];

            switch ($action) {
                case team_request_form_manage::APPROVE:
                    $teamteach->approval_flag = 1;
                    $teamteach->save();
                    $teamteach->apply();
                    break;
                case team_request_form_manage::REVOKE:
                    $toundo[] = $teamteach;
                    break;
                default:
                    // Old: continue;.
                    break;
            }
        }

        $this->undo($toundo);
    }

    public function undo($teamteaches) {
        foreach ($teamteaches as $teamteach) {
            $teamteach->unapply();
            cps_team_request::delete($teamteach->id);
        }
    }

    public function save_or_update($data, $currentteamteaches, $couid, $semid) {
        global $USER;

        foreach (range(1, $data->shells) as $number) {
            $requested = ues_course::get($data->{'query' . $number});
            if (empty($requested)) {
                continue;
            }

            $selected = explode(',', $data->{'selected_users' . $number . '_str'});

            foreach ($selected as $userid) {
                $params = array (
                    'userid' => $USER->id,
                    'courseid' => $couid,
                    'semesterid' => $semid,
                    'requested_course' => $requested->id,
                    'requested' => $userid
                );

                if (!$request = cps_team_request::get($params)) {
                    $request = new cps_team_request();
                    $request->fill_params($params);
                    $request->approval_flag = 0;
                }

                $request->save();
                $request->apply();

                unset ($currentteamteaches[$request->id]);
            }
        }

        $this->undo($currentteamteaches);
    }

    public function display() {
        global $OUTPUT;

        $s = ues::gen_str('block_cps');

        echo $OUTPUT->header();
        echo $OUTPUT->heading($s('team_request_finish'));
        echo $OUTPUT->box_start();
        echo $OUTPUT->notification($s('team_request_thank_you'), 'notifysuccess');
        echo $OUTPUT->continue_button(new moodle_url('/blocks/cps/team_request.php'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
    }
}
