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
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class ues {
    const PENDING = 'pending';
    const PROCESSED = 'processed';

    // Section is created.
    const MANIFESTED = 'manifested';
    const SKIPPED = 'skipped';

    // Teacher / Student manifestation.
    const ENROLLED = 'enrolled';
    const UNENROLLED = 'unenrolled';

    public static function require_libs() {
        self::require_daos();
        self::require_extensions();
    }

    public static function require_daos() {
        $dao = self::base('classes/dao');

        require_once($dao . '/base.php');
        require_once($dao . '/extern.php');
        require_once($dao . '/lib.php');
        require_once($dao . '/daos.php');
        require_once($dao . '/error.php');
        require_once($dao . '/filter.php');
    }

    public static function require_extensions() {
        $classes = self::base('classes');

        require_once($classes . '/processors.php');
        require_once($classes . '/provider.php');
    }

    public static function format_time($time) {
        $ts = intval(core_date::strftime('%Y-%m-%d', $time));
        return $ts;
    }

    public static function where($field = null) {
        return new ues_dao_filter($field);
    }

    public static function inject_manifest(array $sections, $inject = null, $silent = true) {
        self::unenroll_users($sections, $silent);

        if ($inject) {
            foreach ($sections as $section) {
                $inject($section);
            }
        }

        self::enroll_users($sections, $silent);
    }


    /**
     * Unenroll users from the given sections.
     * Note: this will erase the idnumber of the sections
     *
     * @param ues_sections[] $sections
     * @param boolean $silent
     * @return type
     */
    public static function unenroll_users(array $sections, $silent = true) {
        $enrol = enrol_get_plugin('ues');

        $enrol->is_silent = $silent;

        foreach ($sections as $section) {
            $section->status = self::PENDING;
            $section->save();
        }

        $enrol->handle_pending_sections($sections);

        return $enrol->get_errors();
    }

    // Note: this will cause manifestation (course creation if need be).
    public static function enroll_users(array $sections, $silent = true) {
        global $CFG;
        $enrol = enrol_get_plugin('ues');
        $enrol->is_silent = $silent;
        foreach ($sections as $section) {
            foreach (array('teacher', 'student') as $type) {
                $class = 'ues_' . $type;
                $class::reset_status($section, self::PROCESSED);
            }

            $section->status = self::PROCESSED;

            if (file_exists($CFG->dirroot.'/blocks/cps/classes/ues_handler.php')) {
                require_once($CFG->dirroot.'/blocks/cps/classes/ues_handler.php');
                $section = blocks_cps_ues_handler::ues_section_process($section);
            }

            $section->save();
        }

        $enrol->handle_processed_sections($sections);
        return $enrol->get_errors();
    }

    public static function reset_unenrollments(array $sections, $silent = true) {
        $enrol = enrol_get_plugin('ues');

        $enrol->is_silent = $silent;

        foreach ($sections as $section) {
            $enrol->reset_unenrollments($section);
        }

        return $enrol->get_errors();
    }

    public static function reprocess_department($semester, $department, $silent = true) {
        $enrol = enrol_get_plugin('ues');

        if (!$enrol or $enrol->get_errors()) {
            return false;
        }

        if (!$enrol->provider()->supports_department_lookups()) {
            return false;
        }

        $enrol->is_silent = $silent;

        // Work on making department reprocessing code separate.
        ues_error::department($semester, $department)->handle($enrol);

        $ids = ues_section::ids_by_course_department($semester, $department);

        $pending = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PENDING));
        $processed = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PROCESSED));

        $enrol->handle_pending_sections($pending);
        $enrol->handle_processed_sections($processed);

        return true;
    }

    public static function reprocess_course($course, $silent = true) {
        $sections = ues_section::from_course($course, true);

        return self::reprocess_sections($sections, $silent);
    }

    public static function reprocess_sections($sections, $silent = true) {
        $enrol = enrol_get_plugin('ues');

        if (!$enrol or $enrol->get_errors()) {
            return false;
        }

        if (!$enrol->provider()->supports_section_lookups()) {
            return false;
        }

        $enrol->is_silent = $silent;

        foreach ($sections as $section) {
            $enrol->process_enrollment(
                $section->semester(), $section->course(), $section
            );
        }

        $ids = array_keys($sections);

        $pending = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PENDING));
        $processed = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PROCESSED));

        $enrol->handle_pending_sections($pending);
        $enrol->handle_processed_sections($processed);

        return true;
    }

    public static function repall_course($course, $silent = true) {
        $sections = ues_section::from_course($course, true);

        return self::repall_sections($sections, $silent);
    }

    public static function repall_sections($sections, $silent = true) {
        $enrol = enrol_get_plugin('ues');

        if (!$enrol or $enrol->get_errors()) {
            return false;
        }

        if (!$enrol->provider()->supports_section_lookups()) {
            return false;
        }

        $enrol->is_silent = $silent;

        foreach ($sections as $section) {
            $enrol->process_enrollment(
                $section->semester(), $section->course(), $section
            );
        }

        $ids = array_keys($sections);

        $pending = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PENDING));
        $processed = ues_section::get_all(self::where('id')->in($ids)->status->equal(self::PROCESSED));

        $enrol->handle_pending_sections($pending);
        $processedsecs = $enrol->handle_processed_sections($processed);

        return $processedsecs;
    }

    public static function reprocess_for($teacher, $silent = true) {
        $uesuser = $teacher->user();

        $provider = self::create_provider();

        if ($provider and $provider->supports_reverse_lookups()) {
            $enrol = enrol_get_plugin('ues');

            $info = $provider->teacher_info_source();

            $semesters = ues_semester::in_session();

            foreach ($semesters as $semester) {
                $courses = $info->teacher_info($semester, $uesuser);

                $processed = $enrol->process_courses($semester, $courses);

                foreach ($processed as $course) {

                    foreach ($course->sections as $section) {
                        $enrol->process_enrollment(
                            $semester, $course, $section
                        );
                    }
                }
            }

            $enrol->handle_enrollments();
            return true;
        }

        return self::reprocess_sections($teacher->sections(), $silent);
    }

    public static function reprocess_errors($errors, $report = false) {

        $enrol = enrol_get_plugin('ues');

        $amount = count($errors);

        if ($amount) {
            $etxt = $amount === 1 ? 'error' : 'errors';

            $enrol->log('-------------------------------------');
            $enrol->log('Attempting to reprocess ' . $amount . ' ' . $etxt . ':');
            $enrol->log('-------------------------------------');
        }

        foreach ($errors as $error) {
            $enrol->log('Executing error code: ' . $error->name);

            if ($error->handle($enrol)) {
                $enrol->handle_enrollments();
                ues_error::delete($error->id);
            }
        }

        if ($report) {
            $enrol->email_reports();
        }
    }

    public static function drop_semester($semester, $report = false) {
        global $CFG;
        $log = function ($msg) use ($report) {
            if ($report) {
                mtrace($msg);
            }
        };

        $log('Commencing ' . $semester . " drop...\n");
        $count = 0;

        // Remove data from local tables.
        foreach ($semester->sections() as $section) {
            $sectionparam = array('sectionid' => $section->id);
            $types = array('ues_student', 'ues_teacher');

            if (file_exists($CFG->dirroot.'/blocks/ues_logs/eventslib.php')) {
                require_once($CFG->dirroot.'/blocks/ues_logs/eventslib.php');
                ues_logs_event_handler::ues_section_drop($section);
            }

            if (file_exists($CFG->dirroot.'/blocks/cps/classes/ues_handler.php')) {
                require_once($CFG->dirroot.'/blocks/cps/classes/ues_handler.php');
                blocks_cps_ues_handler::ues_section_drop($section);
            }

            if (file_exists($CFG->dirroot.'/blocks/post_grades/events.php')) {
                require_once($CFG->dirroot.'/blocks/post_grades/events.php');
                post_grades_handler::ues_section_drop($section);
            }

            // Optimize enrollment deletion.
            foreach ($types as $class) {
                $class::delete_all(array('sectionid' => $section->id));
            }
            ues_section::delete($section->id);

            $count ++;

            $shouldreport = ($count <= 100 and $count % 10 == 0);
            if ($shouldreport or $count % 100 == 0) {
                $log('Dropped ' . $count . " sections...\n");
            }

            if ($count == 100) {
                $log("Reporting 100 sections at a time...\n");
            }
        }

        $log('Dropped all ' . $count . " sections...\n");

        if (file_exists($CFG->dirroot.'/blocks/cps/classes/ues_handler.php')) {
            require_once($CFG->dirroot.'/blocks/cps/classes/ues_handler.php');
            blocks_cps_ues_handler::ues_semester_drop($semester);
        }
        if (file_exists($CFG->dirroot.'/blocks/post_grades/events.php')) {
            require_once($CFG->dirroot.'/blocks/post_grades/events.php');
            post_grades_handler::ues_semester_drop($semester);
        }

        if(!$semester->semester_ignore){ // LSU Enhancement if semester is ignored do not delete
            ues_semester::delete($semester->id);
        }

        $log('Done');
    }

    public static function gen_str($plugin = 'enrol_ues') {
        return function ($key, $a = null) use ($plugin) {
            return get_string($key, $plugin, $a);
        };
    }

    public static function _s($key, $a=null) {
        return get_string($key, 'enrol_ues', $a);
    }

    public static function format_string($pattern, $obj) {
        foreach (get_object_vars($obj) as $key => $value) {
            $pattern = preg_replace('/\{' . $key . '\}/', $value, $pattern);
        }

        return $pattern;
    }

    public static function base($dir='') {
        return dirname(__FILE__) . (empty($dir) ? '' : '/'.$dir);
    }

    public static function list_plugins() {
        global $CFG;
        $data = new stdClass;
        $data->plugins = array();
        $basedir = $CFG->dirroot.'/local/';
        foreach (scandir($basedir) as $file) {
            if (file_exists($basedir.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'provider.php')) {
                require_once($basedir.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'events.php');
                $class = $file.'_enrollment_events';
                $data = $class::ues_list_provider($data);
            }
        }
        return $data->plugins;
    }

    public static function provider_class() {
        global $CFG;
        $providername = get_config('enrol_ues', 'enrollment_provider');

        if (!$providername) {
            return false;
        }

        $plugins = self::list_plugins();

        if (!isset($plugins[$providername])) {
            return false;
        }

        // Require library code.
        self::require_libs();
        $data = new stdClass;
        $data->provider_class = "{$providername}_enrollment_provider";
        $basedir = $CFG->dirroot.'/local/'.$providername;

        if (file_exists($basedir.'/events.php')) {
            require_once($basedir.'/events.php');
            $class = $providername.'_enrollment_events';
            $fn    = 'ues_load_'.$providername.'_provider';
            $class::$fn($data);
        }
        return $data->provider_class;
    }

    public static function create_provider() {
        $providerclass = self::provider_class();
        return $providerclass ? new $providerclass() : false;
    }

    public static function translate_error($e) {
        $providerclass = self::provider_class();
        $code = $e->getMessage();
        $a = new stdClass;

        if ($code == "enrollment_unsupported") {
            $a->problem = self::_s($code);
        } else {
            $a->problem = $providerclass::translate_error($code);
        }

        $a->pluginname =
            $providerclass ?
            $providerclass::get_name() :
            get_config('enrol_ues', 'enrollment_provider');

        return $a;
    }

    public static function get_task_status_description() {

        $scheduledtask = \core\task\manager::get_scheduled_task('\enrol_ues\task\full_process');

        if ($scheduledtask) {

            $disabled = $scheduledtask->get_disabled();
            $lasttime = $scheduledtask->get_last_run_time();
            $nexttime = $scheduledtask->get_next_scheduled_time();
            $timeformat = '%A, %e %B %G, %l:%M %p';

            $details = new stdClass();
            $details->status = (!$disabled) ? self::_s('run_adhoc_status_enabled') : self::_s('run_adhoc_status_disabled');
            if ($lasttime != "0") {
                $details->last = self::_s('run_adhoc_last_run_time', date_format_string(intval($lasttime), $timeformat, usertimezone()));
            }
            $details->next = self::_s('run_adhoc_next_run_time', date_format_string(intval($nexttime), $timeformat, usertimezone()));

            return self::_s('run_adhoc_scheduled_task_details', $details);
        }

        return false;
    }
}
