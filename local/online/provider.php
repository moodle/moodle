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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/processors.php');
require_once($CFG->dirroot.'/enrol/ues/lib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');

class online_enrollment_provider extends enrollment_provider {
    public $url;
    public $wsdl;
    public $username;
    public $password;

    public $settings = array(
        'credential_location' => 'https://secure.web.lsu.edu/credentials.php',
        'wsdl_location' => 'webService.wsdl',
        'semester_source' => 'ONLINE_MOODLE_SEMESTERS',
        'course_source' => 'MOODLE_COURSES',
        'teacher_by_department' => 'MOODLE_INSTRUCTORS_BY_DEPT',
        'student_by_department' => 'MOODLE_STUDENTS_BY_DEPT',
        'teacher_source' => 'MOODLE_INSTRUCTORS',
        'student_source' => 'MOODLE_STUDENTS',
        'student_data_source' => 'MOODLE_STUDENT_DATA',
        'student_degree_source' => 'MOODLE_DEGREE_CANDIDATE',
        'student_anonymous_source' => 'MOODLE_LAW_ANON_NBR',
        'student_ath_source' => 'MOODLE_STUDENTS_ATH'
    );

    // User data caches to speed things up.
    private $online_degree_cache = array();
    private $online_student_data_cache = array();
    private $online_sports_cache = array();
    private $online_anonymous_cache = array();

    public function init() {
        global $CFG;

        $path = pathinfo($this->wsdl);

        // Path checks.
        if (!file_exists($this->wsdl)) {
            throw new Exception('no_file');
        }

        if ($path['extension'] != 'wsdl') {
            throw new Exception('bad_file');
        }

        if (!preg_match('/^[http|https]/', $this->url)) {
            throw new Exception('bad_url');
        }

        require_once($CFG->libdir . '/filelib.php');

        $curl = new curl(array('cache' => true));
        $resp = $curl->post($this->url, array('credentials' => 'get'));

        list($username, $password) = explode("\n", $resp);

        if (empty($username) or empty($password)) {
            throw new Exception('bad_resp');
        }

        $this->username = trim($username);
        $this->password = trim($password);
    }

    public function __construct($initoncreate = true) {
        global $CFG;

        $this->url = $this->get_setting('credential_location');

        $this->wsdl = $CFG->dataroot . '/'. $this->get_setting('wsdl_location');

        if ($initoncreate) {
            $this->init();
        }
    }

    public function settings($settings) {
        parent::settings($settings);

        $key = $this->plugin_key();
        $s = ues::gen_str($key);

        $optionalpulls = array (
            'student_data' => 1,
            'anonymous_numbers' => 0,
            'degree_candidates' => 0,
            'sports_information' => 1
        );

        foreach ($optionalpulls as $name => $default) {
            $settings->add(new admin_setting_configcheckbox($key . '/' . $name,
                $s($name), $s($name. '_desc'), $default)
            );
        }
    }

    public static function plugin_key() {
        return 'local_online';
    }

    public function semester_source() {
        return new online_semesters(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('semester_source')
        );
    }

    function semester_source2() {
        return new online_semesters(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('semester_source2')
        );
    }

    public function course_source() {
        return new online_courses(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('course_source')
        );
    }

    public function teacher_source() {
        return new online_teachers(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('teacher_source')
        );
    }

    public function student_source() {
        return new online_students(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_source')
        );
    }

    public function student_data_source() {
        return new online_student_data(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_data_source')
        );
    }

    public function anonymous_source() {
        return new online_anonymous(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_anonymous_source')
        );
    }

    public function degree_source() {
        return new online_degree(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_degree_source')
        );
    }

    public function sports_source() {
        return new online_sports(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_ath_source')
        );
    }

    public function teacher_department_source() {
        return new online_teachers_by_department(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('teacher_by_department')
        );
    }

    public function student_department_source() {
        return new online_students_by_department(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_by_department')
        );
    }

    public function preprocess($enrol = null) {
        // Clear student auditing flag on each run; It'll be set in processor.
        return (
            ues_student::update_meta(array('student_audit' => 0)) and
            ues_user::update_meta(array('user_degree' => 0)) and
            // Safe to clear sports on preprocess now that end date is 21 days.
            ues_user::update_meta(array('user_sport1' => '')) and
            ues_user::update_meta(array('user_sport2' => '')) and
            ues_user::update_meta(array('user_sport3' => '')) and
            ues_user::update_meta(array('user_sport4' => ''))
        );
    }

    public function postprocess($enrol = null) {
        $semestersinsession = ues_semester::in_session();

        $now = time();

        $attempts = array(
            'student_data' => $this->student_data_source(),
            'anonymous_numbers' => $this->anonymous_source(),
            'degree_candidates' => $this->degree_source(),
            'sports_information' => $this->sports_source()
        );

        foreach ($semestersinsession as $semester) {

            foreach ($attempts as $key => $source) {
                if (!$this->get_setting($key)) {
                    continue;
                }

                if ($enrol) {
                    $enrol->log("Processing $key for $semester...");
                }

                try {
                    $this->process_data_source($source, $semester);
                } catch (Exception $e) {
                    $handler = new stdClass;

                    $handler->file = '/enrol/ues/plugins/online/errors.php';
                    $handler->function = array(
                        'online_provider_error_handlers',
                        'reprocess_' . $key
                    );

                    $params = array('semesterid' => $semester->id);

                    ues_error::custom($handler, $params)->save();
                }
            }
        }

        return true;
    }

    public function process_data_source($source, $semester) {
        $datas = $source->student_data($semester);

        $name = get_class($source);

        $cache =& $this->{$name . '_cache'};
        foreach ($datas as $data) {
            $params = array('idnumber' => $data->idnumber);

            if (isset($cache[$data->idnumber])) {
                continue;
            }

            $user = ues_user::upgrade_and_get($data, $params);

            if (isset($data->user_college)) {
                $user->department = $data->user_college;
            }

            if (empty($user->id)) {
                continue;
            }

            $cache[$data->idnumber] = $data;

            $user->save();
        }
    }
}
