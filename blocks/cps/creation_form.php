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

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/completionlib.php');

class creation_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $sections = $this->_customdata['sections'];

        $semesters = ues_semester::get_all();

        $courses = array();
        $coursesemesters = array();
        foreach ($sections as $section) {
            $semesterid = $section->semesterid;
            if (!isset($coursesemesters[$semesterid])) {
                $coursesemesters[$semesterid] = array();
            }

            $courseid = $section->courseid;
            if (!isset($courses[$courseid])) {
                $courses[$courseid] = $section->course();
            }

            $coursesemesters[$semesterid][$courseid] = $courses[$courseid];
        }

        unset ($courses, $sections);

        $s = ues::gen_str('block_cps');

        $bold = function ($text) {
            return '<strong>'.$text.'</strong>';
        };

        $spacer = function ($howmany) {
            return array(implode('', array_map(function($i) {
                return '&nbsp;';
            }
            , range(1, $howmany))));
        };

        $defaultcreatedays = get_config('block_cps', 'create_days');
        $defaultenrolldays = get_config('block_cps', 'enroll_days');

        $m->addElement('header', 'defaults', $s('default_settings'));

        $m->addElement('static', 'def_create', $s('default_create_days'),
            $defaultcreatedays);

        $m->addElement('static', 'def_enroll', $s('default_enroll_days'),
            $defaultenrolldays);

        $coursesorter = function($coursea, $courseb) {
            if ($coursea->department == $courseb->department) {
                return strcmp($coursea->cou_number, $courseb->cou_number);
            } else {
                return strcmp($coursea->department, $courseb->department);
            }
        };

        $m->addElement('header', 'create_header', $s('creation_settings'));

        $m->addElement('checkbox', 'creation_defaults', $s('use_defaults'));

        // Add the Appearance option for choosing theme.
        $themeobjects = get_list_of_themes();
        $themes = array();
        $themes[''] = get_string('forceno');
        foreach ($themeobjects as $key => $theme) {
            if (empty($theme->hidefromselector)) {
                $themes[$key] = get_string('pluginname', 'theme_' . $theme->name);
            }
        }
        $m->addElement('select', 'creation_theme', get_string('forcetheme'), $themes);
        $m->disabledIf('creation_theme', 'creation_defaults', 'checked');

        $options = array();
        $formats = get_plugin_list('format');
        foreach ($formats as $format => $ignore) {
            $options[$format] = get_string('pluginname', "format_$format");
        }

        $defaultformat = get_config('moodlecourse', 'format');

        $str = get_string('format');
        $m->addElement('select', "creation_format", $str, $options);
        $m->setDefault('creation_format', $defaultformat);
        $m->disabledIf('creation_format', 'creation_defaults', 'checked');

        $maxsections = get_config('moodlecourse', 'maxsections');
        $defaultnumber = get_config('moodlecourse', 'numsections');
        $options = array_combine(range(1, $maxsections), range(1, $maxsections));
        $str = get_string('numberweeks');
        $m->addElement('select', 'creation_numsections', $str, $options);
        $m->setDefault('creation_numsections', $defaultnumber);
        $m->disabledIf('creation_numsections', 'creation_defaults', 'checked');

        $defaultvisibility = get_config('moodlecourse', 'visible');
        $options = array(
            '0' => get_string('courseavailablenot'),
            '1' => get_string('courseavailable')
        );
        $str = get_string('availability');
        $m->addElement('select', 'creation_visible', $str, $options);
        $m->setDefault('creation_visible', $defaultvisibility);
        $m->disabledIf('creation_visible', 'creation_defaults', 'checked');

        if (completion_info::is_enabled_for_site()) {
            $options = array(
                0 => get_string('completiondisabled', 'completion'),
                1 => get_string('completionenabled', 'completion')
            );

            $m->addElement('static', '', '<strong>' . get_string('progress', 'completion') . '</strong>', '');
            $m->addElement('select', 'creation_enablecompletion', get_string('completion', 'completion'), $options);
            $m->setDefault('creation_enablecompletion', get_config('moodlecourse', 'enablecompletion'));
            $m->disabledIf('creation_enablecompletion', 'creation_defaults', 'checked');
        }

        foreach ($coursesemesters as $semesterid => $courses) {
            uasort($courses, $coursesorter);

            $semester = $semesters[$semesterid];
            $name = "{$semester->year} {$semester->name}";

            $m->addElement('header', 'semester_' . $semesterid, $name);

            $label = array(
                $m->createElement('static', 'label', '', $bold($s('create_days'))),
                $m->createElement('static', 'label', '', $bold($s('enroll_days')))
            );

            $m->addGroup($label, 'labels', '&nbsp;', $spacer(15));

            $createdefault = get_config('block_cps', 'create_days');
            $enrolldefault = get_config('block_cps', 'enroll_days');

            foreach ($courses as $courseid => $course) {
                $id = "{$semesterid}_{$courseid}";

                $group = array(
                    $m->createElement('text', 'create_days_'.$id, null),
                    $m->createElement('text', 'enroll_days_'.$id, null)
                );

                $m->addGroup($group, 'create_group_'.$id, $course, $spacer(1));
                $m->setType("create_group_{$id}[create_days_{$id}]", PARAM_INT);
                $m->setDefault("create_group_{$id}[create_days_{$id}]", $createdefault);
                $m->setType("create_group_{$id}[enroll_days_{$id}]", PARAM_INT);
                $m->setDefault("create_group_{$id}[enroll_days_{$id}]", $enrolldefault);
            }
        }

        $buttons = array(
            $m->createElement('submit', 'save', get_string('savechanges')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'buttons', '', $spacer(1), false);
        $m->closeHeaderBefore('buttons');
    }

    public function validation($data, $files) {
        $create_days = $enroll_days = $settings = $errors = array();

        $fill = function (&$collection, $semesterid, $courseid, $value) {
            if (!isset($collection[$semesterid])) {
                $collection[$semesterid] = array();
            }

            $numeric = is_numeric($value) && (int) $value > 0;
            $emptystr = trim($value) === '';

            if ($numeric || $emptystr) {
                $value = $numeric ? (int) $value : $value;
                $collection[$semesterid][$courseid] = $value;
                return true;
            } else {
                return false;
            }
        };

        $s = ues::gen_str('block_cps');

        // Iterate over the form values for creation and enrollment data.
        foreach ($data as $gname => $group) {
            if ($gname === 'creation_defaults') {
                continue;
            }

            if (preg_match('/^creation_/', $gname)) {
                $settings[$gname] = $group;
                continue;
            }

            if (preg_match('/^create_group_(\d+)_(\d+)/', $gname, $matches)) {
                $semesterid = $matches[1];
                $courseid = $matches[2];

                foreach ($group as $name => $value) {
                    if (preg_match('/^create_days/', $name)) {
                        $filled = $fill($create_days, $semesterid,
                            $courseid, $value);
                        if (!$filled) {
                            if (!is_numeric($value)) {
                                $errors[$gname] = $s('err_numeric');
                            } else {
                                $errors[$gname] = $s('err_number');
                            }
                            break;
                        }
                    } else {
                        $filled = $fill($enroll_days, $semesterid, $courseid, $value);

                        $valid = true;
                        if (!$filled) {
                            if (!is_numeric($value)) {
                                $errors[$gname] = $s('err_numeric');
                            } else {
                                $errors[$gname] = $s('err_number');
                            }
                            break;
                        } else {
                            if ($create_days[$semesterid][$courseid] == '' &&
                                    $enroll_days[$semesterid][$courseid] == '') {
                                $bothempty = true;
                            } else {
                                $bothempty = false;
                                if (is_numeric($create_days[$semesterid][$courseid]) &&
                                        is_numeric($enroll_days[$semesterid][$courseid])) {
                                    $bothnumeric = true;
                                } else {
                                    $bothnumeric = false;
                                }

                                if ($filled && !$bothempty && !$bothnumeric) {
                                    $errors[$gname] = $s('err_both_empty');
                                } else {
                                    $valid = ($create_days[$semesterid][$courseid] >= $value);

                                    if ($filled and !$valid) {
                                        $errors[$gname] = $s('err_enrol_days');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->create_days = $create_days;
        $this->enroll_days = $enroll_days;
        $this->settings = $settings;

        return $errors;
    }
}