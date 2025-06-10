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

require_once('../../config.php');
require_once('classes/lib.php');
require_once('creation_form.php');

require_login();

if (!cps_creation::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_creation::name());
}

if (!ues_user::is_teacher()) {
    print_error('not_teacher', 'block_cps');
}

$teacher = ues_teacher::get(array('userid' => $USER->id));

// We're only concerned with active sections.
$all        = $teacher->sections(true);
$filter     = ues::where()->grades_due->greater_equal(time());
$valids     = array_keys(ues_semester::get_all($filter));
$sections   = array();
foreach ($all as $sec) {
    if (in_array($sec->semesterid, $valids)) {
        $sections[] = $sec;
    }
}


if (empty($sections)) {
    print_error('no_section', 'block_cps');
}

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_creation::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': '. $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/cps/creation.php');

$form = new creation_form(null, array('sections' => $sections));

$settingparams = ues::where()
    ->userid->equal($USER->id)
    ->name->starts_with('creation_');

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $form->get_data()) {
    $settings  = cps_setting::get_to_name($settingparams);
    $creations = cps_creation::get_all(array('userid' => $USER->id));

    if (isset($data->creation_defaults)) {
        cps_setting::delete_all($settingparams);
    }

    foreach ($form->settings as $name => $value) {
        if (!isset($settings[$name])) {
            $setting = new cps_setting();
            $setting->name = $name;
            $setting->userid = $USER->id;
            $setting->value = null;
        } else {
            $setting = $settings[$name];
        }

        if ($setting->value == $value) {
            continue;
        }

        $setting->value = $value;
        $setting->save();
    }

    $moodle_course_visibilities = array();
    $cpss = get_config('block_cps');
    foreach ($form->create_days as $semesterid => $courses) {
        foreach ($courses as $courseid => $create_days) {
            if (empty($create_days)) {
                continue;
            }

            $enroll_days = $form->enroll_days[$semesterid][$courseid];

            $params = array(
                'userid' => $USER->id,
                'semesterid' => $semesterid,
                'courseid' => $courseid
            );

            $creation = cps_creation::get($params);
            if (!$creation) {
                $creation = new cps_creation();
                $creation->fill_params($params);
            }

            $samevalue = $creation->create_days == $create_days && $creation->enroll_days == $enroll_days;
            $defaultvalue = $cpss->create_days == $create_days && $cpss->enroll_days == $enroll_days;
            $no_value   = $creation->create_days == null && $creation->enroll_days == null;
            if ($samevalue) {
                // If nothing has changed, skip the rest of the loop:
                // Apply will perform unenroll/enroll, causing course visibility = 0.
                unset($creations[$creation->id]);
                continue;
            } else if ($defaultvalue) {
                global $DB;
                $creation->create_days = $cpss->create_days;
                $creation->enroll_days = $cpss->enroll_days;
                $creation->save();
                continue;
            } else {
                global $DB;
                $creation->create_days = $create_days;
                $creation->enroll_days = $enroll_days;
                $creation->save();

                // Populate the moodle_course_visibilities map for all courses in which the current user is primary.
                $course = ues_course::by_id($courseid);
                $sections = $course->sections(ues_semester::by_id($semesterid));
                foreach ($sections as $section) {
                    if ($USER->id != $section->primary()->userid) {
                        continue;
                    }
                    $moodle_course = $section->moodle();
                    if ($moodle_course and !array_key_exists($moodle_course->id, $moodle_course_visibilities)) {
                        $moodle_course_visibilities[$moodle_course->id] = $moodle_course->visible;
                    }
                }

                $creation->apply();
            }
            unset($creations[$creation->id]);
        }
    }

    // Reset the visibility for courses made invisible by $creation->apply(); HACK!.
    $moodle_courses = $DB->get_records_list('course', 'id', array_keys($moodle_course_visibilities));
    foreach ($moodle_courses as $id => $course) {
        if ($course->visible != $moodle_course_visibilities[$id]) {
            $course->visible = $moodle_course_visibilities[$id];
            $DB->update_record('course', $course);
        }
    }

    foreach ($creations as $creation) {
        cps_creation::delete($creation->id);
        $creation->apply();
    }

    $success = true;
}

$creations = cps_creation::get_all(array('userid' => $USER->id));
$settings  = cps_setting::get_all($settingparams);

$form_data = array();

if (empty($settings)) {
    $form_data['creation_defaults'] = 1;
}

foreach ($settings as $setting) {
    $form_data[$setting->name] = $setting->value;
}

foreach ($creations as $creation) {
    $semesterid = $creation->semesterid;
    $courseid   = $creation->courseid;

    $id = "_{$semesterid}_{$courseid}";

    $form_data["create_group{$id}[create_days{$id}]"] = $creation->create_days;
    $form_data["create_group{$id}[enroll_days{$id}]"] = $creation->enroll_days;
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'creation', 'block_cps');

if (isset($success) and $success) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
} else if ($form->is_submitted() && !$form->is_validated()) {
    echo $OUTPUT->notification(get_string('someerrorswerefound'));
}

$form->set_data($form_data);
$form->display();

echo $OUTPUT->footer();
