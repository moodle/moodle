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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/cps/formslib.php');

abstract class team_section_form extends cps_form {
    public function extract_data() {
        return array(
            $this->_customdata['course'],
            $this->_customdata['semester'],
            $this->_customdata['requests']
        );
    }
}

class team_section_form_select extends team_section_form {
    public $current = self::SELECT;
    public $next = self::SHELLS;

    public static function build($courses) {
        return $courses;
    }

    public function definition() {
        $m =& $this->_form;

        list($course, $semester, $requests) = $this->extract_data();
        $m->addElement('header', 'selected_course',
            $this->display_course($course, $semester));

        $isamaster = false;
        $notamaster = false;

        $m->addElement('static', 'your_sections', self::_s('team_section'), '');

        foreach ($requests as $request) {
            if ($request->is_owner()) {
                $isamaster = true;
                $courseid = $request->courseid;
                $othercourse = $request->other_course();
            } else {
                $notamaster = true;
                $courseid = $request->requested_course;
                $othercourse = $request->course();
            }

            if ($course->id == $courseid) {
                foreach ($course->sections as $section) {
                    $label = 'Section ' . $section->sec_number;

                    if (cps_team_section::exists($section)) {
                        $label .= ' ' . self::_s('team_section_option');
                    }

                    $m->addElement('static', 'section_' . $section->courseid, '',
                        $label);
                }

                $m->addElement('static', 'other_course_' . $othercourse->id,
                    $this->display_course($othercourse, $semester), '');

                foreach ($request->sections() as $tsec) {
                    $section = $tsec->section();

                    if ($section->courseid != $courseid) {
                        $m->addElement('static', 'other_selected_' . $section->id,
                            '', 'Section ' . $section->sec_number);
                    }
                }
            }
        }

        $m->addElement('static', 'breather', '', '');
        $m->addElement('static', 'continue_build', '', self::_s('team_continue_build'));
        $m->addElement('hidden', 'id', $semester->id . '_' . $course->id);
        $m->setType('id', PARAM_ALPHANUMEXT);

        $this->is_a_master = $isamaster;
        $this->not_a_master = $notamaster;

        if ($notamaster and !$isamaster) {
            $allsections = cps_team_section::in_requests($requests);

            $merged = cps_team_section::merge_groups($allsections);

            if (empty($merged)) {
                $m->addElement('static', 'team_note', '',
                    self::_s('team_section_note'));

                $this->next = null;
            } else {
                $m->addElement('hidden', 'shells', count($merged));
                $m->setType('shells', PARAM_INT);

                foreach ($merged as $number => $sections) {
                    $namekey = 'shell_name_' . $number . '_hidden';
                    $valuekey = 'shell_values_' . $number;

                    $m->addElement('hidden', $namekey,
                        current($sections)->shell_name);
                    $m->setType($namekey, PARAM_TEXT);

                    $tosectionids = function ($sec) {
                        return $sec->sectionid;
                    };

                    $m->addElement('hidden', $valuekey,
                        implode(',', array_map($tosectionids, $sections)));
                    $m->setType($valuekey, PARAM_RAW);
                }
            }
        }

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        // Teacher can potentially be both...
        // But if the Teacher is NOT a master, then we can skip the shells.
        if ($this->not_a_master and !$this->is_a_master) {
            // Make sure the master has created shells.
            $this->next = self::DECIDE;
        }

        $requests = $this->_customdata['requests'];
        $course = $this->_customdata['course'];

        $updating = cps_team_section::in_sections($requests, $course->sections);

        if ($this->is_a_master and $updating) {
            $this->next = self::UPDATE;
        }

        return true;
    }
}

class team_section_form_update extends team_section_form implements updating_form {
    public $current = self::UPDATE;
    public $next = self::DECIDE;
    public $prev = self::SELECT;

    public static function build($courses) {
        return self::prep_reshell() + team_section_form_shells::build($courses);
    }

    public function definition() {
        $m =& $this->_form;

        list($course, $semester, $requests) = $this->extract_data();

        $todisplay = $this->to_display($semester);

        $mergedsections = cps_team_section::merge_groups_in_requests($requests);

        $shells = count($mergedsections);

        $sectioncount = 0;
        $courses = array();
        $users = array();

        $m->addElement('header', 'selected_course', $todisplay($course));

        foreach ($mergedsections as $number => $reqsecs) {
            $shell_name = current($reqsecs)->shell_name;
            $shell_values = array();

            $m->addElement('static', 'shell_' . $number, $shell_name, '');
            foreach ($reqsecs as $req_sec) {
                $section = $req_sec->section();

                $primary = $section->primary();

                if (!isset($courses[$section->courseid])) {
                    $courses[$section->courseid] = $section->course();
                }

                if (!isset($users[$primary->userid])) {
                    $users[$primary->userid] = $primary->user();

                    $sections = $primary->sections(true);

                    $taught = ues_course::merge_sections($sections);
                    $sectioncount += count($taught[$section->courseid]->sections);
                }

                $c = $courses[$section->courseid];
                $user = $users[$primary->userid];

                $label = array(
                    "$c->department $c->cou_number",
                    'Section',
                    $section->sec_number,
                    'for',
                    fullname($user)
                );

                $m->addElement('static', 'section_' . $section->id, '',
                    implode(' ', $label));

                $shell_values[] = $section->id;
            }

            $m->addElement('hidden', 'shell_name_' . $number . '_hidden', $shell_name);
            $m->addElement('hidden', 'shell_values_' . $number, implode(',', $shell_values));
        }

        $m->addElement('static', 'breather', '', '');

        $reshell_max = floor($sectioncount / count($courses));

        $m->addElement('radio', 'team_section_option', '', self::_s('split_undo'), self::UNDO);

        if ($reshell_max > 1) {
            $reshell_range = range(1, $reshell_max);
            $options = array_combine($reshell_range, $reshell_range);

            $m->addElement('radio', 'team_section_option', '', self::_s('split_reshell'), self::RESHELL);
            $m->addElement('select', 'reshelled', self::_s('split_how_many'), $options);
            $m->addHelpButton('reshelled', 'split_how_many', 'block_cps');

            $m->disabledIf('reshelled', 'team_section_option', 'neq', self::RESHELL);
        }

        $m->addElement('radio', 'team_section_option', '', self::_s('split_rearrange'), self::REARRANGE);

        $m->setDefault('team_section_option', self::REARRANGE);

        $m->addElement('hidden', 'shells', $shells);
        $m->addElement('hidden', 'id', '');
        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        if (isset($data['back'])) {
            return true;
        }

        $option = $data['team_section_option'];

        $this->next = $option == self::UNDO ? self::LOADING : $this->next;

        return true;
    }
}

class team_section_form_shells extends team_section_form {
    public $current = self::SHELLS;
    public $prev = self::SELECT;
    public $next = self::DECIDE;

    public static function build($courses) {
        return team_section_form_select::build($courses);
    }

    public function definition() {
        $m =& $this->_form;

        list($course, $semester, $requests) = $this->extract_data();

        $m->addElement('header', 'selected_course',
            $this->display_course($course, $semester));

        $sectioncount = count($course->sections);

        $other_courses = array();

        foreach ($requests as $request) {
            if (!$request->is_owner()) {
                continue;
            }

            $id = $request->requested_course;
            if (!isset($other_courses[$id])) {
                $other_courses[$id] = $request->other_course();
            }

            $teacher = $request->other_teacher();

            $taught_courses = ues_course::merge_sections($teacher->sections(true));

            $sectioncount += count($taught_courses[$id]->sections);
        }

        $shell_range = range(1, floor($sectioncount / (count($other_courses) + 1)));

        $options = array_combine($shell_range, $shell_range);

        $m->addElement('select', 'shells', self::_s('split_how_many'), $options);
        $m->addHelpButton('shells', 'split_how_many', 'block_cps');

        $m->addElement('hidden', 'id', $course->id);
        $m->setType('id', PARAM_ALPHANUMEXT);
        $this->generate_states_and_buttons();
    }
}

class team_section_form_decide extends team_section_form {
    public $current = self::DECIDE;
    public $next = self::CONFIRM;
    public $prev = self::SHELLS;

    public static function build($courses) {
        return self::conform_reshell() + team_section_form_shells::build($courses);
    }

    public function definition() {
        $m =& $this->_form;

        list($course, $semester, $requests) = $this->extract_data();

        $display = "$semester->year $semester->name" . $semester->get_session_key();

        $all_courses = array(); 

        $allsections = $course->sections;

        $to_coursename = function ($course) {
            return "$course->department $course->cou_number";
        };

        $before = array();

        $mastered = cps_team_request::filtered_master($requests);

        $tt_sections = cps_team_section::in_requests($requests);

        foreach ($tt_sections as $section) {
            if (isset($allsections[$section->sectionid])) {
                continue;
            }

            $section = $section->section();
            $allsections[$section->id] = $section;
        }

        foreach ($requests as $request) {
            // Make sure that course and other_course member objects have been added to this $request.
            $request->course();
            $request->other_course();

            // Add course and other_course from this $request to $all_courses (if not already added).
            if (!isset($all_courses[$request->courseid])) {
                $all_courses[$request->courseid] = $request->course();
            }
            if (!isset($all_courses[$request->requested_course])) {
                $all_courses[$request->requested_course] = $request->other_course();
            }
        }

        $all_names = implode(' / ', array_map($to_coursename, $all_courses));

        $m->addElement('header', 'selected_course', "$display $all_names");

        foreach ($allsections as $section) {
            $before[$section->id] =
                $to_coursename($all_courses[$section->courseid]) . ' ' .
                $section->sec_number;

            if (isset($course->sections[$section->id])) {
                $before[$section->id] .= ' ' . $this->_s('team_section_yours');
            }
        }

        $shells = array();

        $number = $this->_customdata['shells'] + $this->_customdata['reshelled'];

        foreach (range(1, $number) as $groupingid) {
            $updating = !empty($this->_customdata['shell_values_' . $groupingid]);

            if ($updating) {
                $shell_name_value = $this->_customdata['shell_name_' . $groupingid . '_hidden'];
                $shell_values = $this->_customdata['shell_values_' . $groupingid];

                $shell_ids = explode(',', $shell_values);
                $shell_sections = array_map(function($sec) use ( &$before) {
                    $section = $before[$sec];
                    unset($before[$sec]);
                    return $section;
                }, $shell_ids);

                $shell_options = array_combine($shell_ids, $shell_sections);
            } else {
                $shell_name_value = 'Team ' . $groupingid . ' ' . $all_names;
                $shell_values = '';

                $shell_options = array();
            }

            $shell_label =& $m->createElement('static', 'shell_' . $groupingid .
                '_label', '', $display . ' <span id="shell_name_' . $groupingid . '">'
                . $shell_name_value . '</span>');
            $shell =& $m->createElement('select', 'shell_' . $groupingid, '', $shell_options);
            $shell->setMultiple(true);

            $shell_name_params = array('style' => 'display: none;');
            $shell_name =& $m->createElement('text', 'shell_name_' . $groupingid,
                '', $shell_name_params);
            $shell_name->setValue($shell_name_value);

            $link = empty($mastered) ? get_string('locked', 'grades') :
                html_writer::link('shell_' . $groupingid, self::_s('customize_name'));

            $radio_params = array('id' => 'selected_shell_' . $groupingid);
            $radio =& $m->createElement('radio', 'selected_shell', '', '', $groupingid, $radio_params);

            $radio->setChecked($groupingid == 1);

            $shells[] = $shell_label->toHtml() . ' (' . $link . ')<br/>' .
                $shell_name->toHtml() . '<br/>' . $radio->toHtml() . $shell->toHtml();

            $m->addElement('hidden', 'shell_values_' . $groupingid, $shell_values);
            $m->setType('shell_values_' . $groupingid, PARAM_RAW);

            $m->addElement('hidden', 'shell_name_' . $groupingid . '_hidden', $shell_name_value);
            $m->setType('shell_name_' . $groupingid . '_hidden', PARAM_TEXT);

        }

        $previous_label =& $m->createElement('static', 'available_sections',
            '', self::_s('available_sections'));

        $previous =& $m->createElement('select', 'before', '', $before);
        $previous->setMultiple(true);

        $form_html = $this->mover_form($previous_label, $previous, $shells);

        $m->addElement('html', $form_html);
        $m->addElement('hidden', 'shells', '');
        $m->setType('shells', PARAM_INT);
        $m->addElement('hidden', 'id', '');
        $m->setType('id', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'team_section_option', '');
        $m->setType('team_section_option', PARAM_ALPHANUMEXT);
        $m->addElement('hidden', 'reshelled', '');
        $m->setType('reshelled', PARAM_INT);

        $this->generate_states_and_buttons();
    }

    public function validation($data, $files) {
        $requests = $this->_customdata['requests'];

        $mastered = cps_team_request::filtered_master($requests);

        $option = $data['team_section_option'];

        if (isset($data['back'])) {
            if ($option == team_section_form_update::RESHELL or
                $option == team_section_form_update::REARRANGE) {
                $this->prev = self::UPDATE;

            } else {

                $this->prev = empty($mastered) ? self::SELECT : $this->prev;
            }
            return true;
        }
        // Did they try to move a section they didn't own?
        // TODO: this is awful... I'd like a better solution.
        if (empty($mastered)) {
            $course = $this->_customdata['course'];

            $sections = cps_team_section::merge_groups_in_requests($requests);

            foreach (range(1, $data['shells']) as $number) {
                $sectionids = explode(',', $data['shell_values_' . $number]);

                if (isset($sections[$number])) {
                    foreach ($sections[$number] as $sec) {
                        if (isset($course->sections[$sec->sectionid])) {
                            continue;
                        }

                        if (!in_array($sec->sectionid, $sectionids) ) {
                            return array('shifters' =>
                                self::_s('team_section_no_permission'));
                        }
                    }
                }
            }
        }
    }
}

class team_section_form_confirm extends team_section_form {
    public $current = self::CONFIRM;
    public $prev = self::DECIDE;
    public $next = self::LOADING;

    public static function build($courses) {
        $data = team_section_form_decide::build($courses);

        $extra = array();

        foreach (range(1, $data['shells']) as $number) {
            $valuekey = 'shell_values_' . $number;
            $namekey = 'shell_name_' . $number . '_hidden';

            $extra += array(
                $namekey => required_param($namekey, PARAM_TEXT),
                $valuekey => required_param($valuekey, PARAM_RAW)
            );
        }

        return $extra + $data;
    }

    public function definition() {
        $m =& $this->_form;

        list($course, $semester, $requests) = $this->extract_data();

        $display = "{$semester->year} {$semester->name}" . $semester->get_session_key();

        $to_coursename = function ($course) {
            return "{$course->department} {$course->cou_number}";
        };

        $courses = array($course->id => $to_coursename($course));

        foreach ($requests as $request) {
            $othercourse = $request->is_owner() ?
                $request->other_course() : (
                    $request->is_other_user() ? $request->course() : $request->other_course()
                );

            if (isset($courses[$othercourse->id])) {
                continue;
            }

            $courses[$othercourse->id] = $to_coursename($othercourse);
        }

        $all_courses = implode(' / ', $courses);

        $all_users = array();

        $m->addElement('header', 'selected_course', "{$display} {$all_courses}");

        foreach (range(1, $this->_customdata['shells']) as $number) {
            $valuekey = 'shell_values_' . $number;
            $namekey = 'shell_name_' . $number . '_hidden';

            $sectionids = $this->_customdata[$valuekey];
            $shell_name = $this->_customdata[$namekey];

            $sections = ues_section::get_all(ues::where('id')->in(explode(',', $sectionids)));

            $m->addElement('static', 'shell_' . $number, $shell_name, '');

            foreach ($sections as $section) {
                $teacher = $section->primary();

                if (!isset($all_users[$teacher->userid])) {
                    $all_users[$teacher->userid] = $teacher->user();
                }

                $parts = array(
                    $courses[$section->courseid],
                    $section->sec_number,
                    'for',
                    fullname($all_users[$teacher->userid])
                );

                $label = implode(' ', $parts);

                $m->addElement('static', 'section_' . $section->id, '', $label);
            }

            $m->addElement('hidden', $valuekey, '');
            $m->addElement('hidden', $namekey, '');
            $m->setType($namekey, PARAM_TEXT);
            $m->setType($valuekey, PARAM_RAW);
        }

        $m->addElement('hidden', 'id', '');
        $m->setType('id', PARAM_ALPHANUMEXT);

        $m->addElement('hidden', 'shells', '');
        $m->setType('shells', PARAM_INT);

        $m->addElement('hidden', 'team_section_option', '');
        $m->setType('team_section_option', PARAM_INT);

        $this->generate_states_and_buttons();
    }
}

class team_section_form_finish implements finalized_form, updating_form {
    public $id;

    public function process($data, $initial_data) {
        $this->id = $data->id;

        $requests = $initial_data['requests'];
        $course = $initial_data['course'];

        $mastered = cps_team_request::filtered_master($requests);
        if (empty($mastered)) {
            $sections = cps_team_section::in_sections($requests, $course->sections);
        } else {
            $sections = cps_team_section::in_requests($requests);
        }

        $updating = !empty($data->team_section_option);

        if ($updating and $data->team_section_option == self::UNDO) {
            $this->undo($sections);
        } else {
            $this->save_or_update($data, $requests, $sections);
        }
    }

    public function undo($currentsections) {
        foreach ($currentsections as $section) {
            $section->delete($section->id);
            $section->unapply();
        }
    }

    public function save_or_update($data, $currentrequests, $currentsections) {

        foreach (range(1, $data->shells) as $number) {
            $sectionids = $data->{'shell_values_' . $number};
            $shell_name = $data->{'shell_name_' . $number . '_hidden'};

            foreach (explode(',', $sectionids) as $sectionid) {
                $section = ues_section::get(array('id' => $sectionid));

                $requested = $section->primary();

                $associates = function ($req) use ($requested, $section) {
                    $comp = array(0 => $req->semesterid);

                    if ($req->is_owner($requested->userid)) {
                        $comp += array(
                            1 => $req->userid,
                            2 => $req->courseid);
                    } else {
                        $comp += array(
                            1 => $req->requested,
                            2 => $req->requested_course);
                    }

                    list($semid, $userid, $courseid) = $comp;

                    return $requested->userid == $userid and
                        $section->semesterid == $semid and
                        $section->courseid == $courseid;
                };

                $associate = current(array_filter($currentrequests, $associates));

                $params = array('sectionid' => $sectionid);

                // Interesting ... probably don't have access; skip.
                if (empty($associate)) {
                    $reqsecs = cps_team_section::get_all($params);

                    foreach ($reqsecs as $req_sec) {
                        unset($currentsections[$req_sec->id]);
                    }
                    continue;
                }

                $params += array(
                    'courseid' => $associate->courseid,
                    'requesterid' => $associate->userid
                );

                // Don't want to mess with requester's requestid.
                $req_sec = cps_team_section::get($params);

                if (!$req_sec) {
                    $params['requestid'] = $associate->id;

                    // Potentially update own section.
                    if (!$req_sec = cps_team_section::get($params)) {
                        $req_sec = new cps_team_section();
                        $req_sec->fill_params($params);
                    }
                }

                $req_sec->groupingid = $number;
                $req_sec->shell_name = $shell_name;

                $req_sec->save();
                $req_sec->apply();

                unset($currentsections[$req_sec->id]);
            }
        }

        $this->undo($currentsections);
    }

    public function display() {
        global $OUTPUT;

        $s = ues::gen_str('block_cps');

        echo $OUTPUT->notification($s('team_section_processed'), 'notifysuccess');

        $params = array('id' => $this->id);
        $url = new moodle_url('/blocks/cps/team_section.php', $params);

        echo $OUTPUT->continue_button($url);
    }
}