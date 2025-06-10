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
require_once dirname(__FILE__) . '/processors.php';
require_once($CFG->dirroot.'/enrol/ues/lib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');

class azure_enrollment_provider extends enrollment_provider {
    public $url;
    public $wsdl;
    public $username;
    public $password;

    public $settings = array(
        'credential_location' => 'https://secure.web.azure.edu/credentials.php',
        'wsdl_location' => 'webService.wsdl',
        'semester_source' => 'MOODLE_SEMESTERS',
        'semester_source2' => 'ONLINE_SEMESTERS',
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
    private $azure_degree_cache = array();
    private $azure_student_data_cache = array();
    private $azure_sports_cache = array();
    private $azure_anonymous_cache = array();

    function init() {
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

        require_once $CFG->libdir . '/filelib.php';

        // Make sure we only grab remote credentials when moodleftp is specified.
        if (get_config('enrol_ues', 'username') == 'moodleftp') {
            // mtrace("Utilizing remote credentials location.");

            // Instantiate curl.
            $curl = new curl(array('cache' => true));

            // Get the credentials.
            $resp = $curl->post($this->url, array('credentials' => 'get'));

            // Split out the username and password accordingly.
            list($username, $password) = explode("\n", $resp);
        } else {
            // mtrace("Utilizing locally stored credentials.");

            // Get the username stored in config.
            $username = get_config('enrol_ues', 'username');

            // Get the password stored in config.
            $password = get_config('enrol_ues', 'password');
        }

        if (empty($username) or empty($password)) {
            throw new Exception('bad_resp');
        }

        $this->username = trim($username);
        $this->password = trim($password);
    }

    function __construct($init_on_create = true) {
        global $CFG;

        $this->url = $this->get_setting('credential_location');

        $this->wsdl = $CFG->dataroot . '/'. $this->get_setting('wsdl_location');

        if ($init_on_create) {
            $this->init();
        }
    }

    public function settings($settings) {
        parent::settings($settings);

        $key = $this->plugin_key();
        $_s = ues::gen_str($key);

        $optional_pulls = array (
            'student_data' => 1,
            'anonymous_numbers' => 0,
            'degree_candidates' => 0,
            'sports_information' => 1
        );

        foreach ($optional_pulls as $name => $default) {
            $settings->add(new admin_setting_configcheckbox($key . '/' . $name,
                $_s($name), $_s($name. '_desc'), $default)
            );
        }

        // June date.
        $settings->add(
            new admin_setting_configtext(
                'local_azure/junedate',
                get_string('azure_junedate', 'local_azure'),
                get_string('azure_junedate_desc', 'local_azure'),
                604  // Default.
            )
        );

        // December date.
        $settings->add(
            new admin_setting_configtext(
                'local_azure/decemberdate',
                get_string('azure_decemberdate', 'local_azure'),
                get_string('azure_decemberdate_desc', 'local_azure'),
                1231  // Default.
            )
        );
    }

    public static function plugin_key() {
        return 'local_azure';
    }

    function semester_source() {
        return new azure_semesters(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('semester_source')
        );
    }

    function semester_source2() {
        return new azure_semesters(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('semester_source2')
        );
    }

    function course_source() {
        return new azure_courses(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('course_source')
        );
    }

    function teacher_source() {
        return new azure_teachers(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('teacher_source')
        );
    }

    function student_source() {
        return new azure_students(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_source')
        );
    }

    function student_data_source() {
        return new azure_student_data(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_data_source')
        );
    }

    function anonymous_source() {
        return new azure_anonymous(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_anonymous_source')
        );
    }

    function degree_source() {
        return new azure_degree(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_degree_source')
        );
    }

    function sports_source() {
        return new azure_sports(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_ath_source')
        );
    }

    function teacher_department_source() {
        return new azure_teachers_by_department(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('teacher_by_department')
        );
    }

    function student_department_source() {
        return new azure_students_by_department(
            $this->username, $this->password,
            $this->wsdl, $this->get_setting('student_by_department')
        );
    }

    function preprocess($enrol = null) {
        $semesters_in_session = ues_semester::in_session();

        foreach ($semesters_in_session as $semester) {
            // Cleanup orphaned groups- https://trello.com/c/lQqVUrpQ.
            // REMOVED 08/20/2020 to speed things up - and it's not really doing anything.
            // $orphanedGroupMemebers = $this->findOrphanedGroups($semester);
            // $this->unenrollGroupsUsers($orphanedGroupMemebers);

            // Find and remove any duplicate group membership records.
            $duplicateGroupMemberships = $this->findDuplicateGroupMembers($semester);
            $this->removeGroupDupes($duplicateGroupMemberships);
        }

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

    function postprocess($enrol = null) {
        $semesters_in_session = ues_semester::in_session();

        $now = time();

        $attempts = array(
            'student_data' => $this->student_data_source(),
            'anonymous_numbers' => $this->anonymous_source(),
            'degree_candidates' => $this->degree_source(),
            'sports_information' => $this->sports_source()
        );

        foreach ($semesters_in_session as $semester) {

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

                    echo"<br /> \n exception: ";
                    var_dump($e);
                    echo"<br /> \n";

                    $handler = new stdClass;

                    $handler->file = '/enrol/ues/plugins/azure/errors.php';
                    $handler->function = array(
                        'azure_provider_error_handlers',
                        'reprocess_' . $key
                    );

                    $params = array('semesterid' => $semester->id);

                    ues_error::custom($handler, $params)->save();
                }
            }
        }

        return true;
    }

    function process_data_source($source, $semester) {
        global $CFG;

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

            // Todo: Refactor to actually use Event 2 rather than simply calling the handlers directly.
            require_once($CFG->dirroot . '/blocks/cps/classes/ues_handler.php');
            switch ($name) {
                case 'azure_student_data': {
                    blocks_cps_ues_handler::ues_azure_student_data_updated($user);
                    break;
                }
                case 'xml_student_data': {
                    blocks_cps_ues_handler::ues_xml_student_data_updated($user);
                    break;
                }
                case 'azure_anonymous': {
                    blocks_cps_ues_handler::ues_azure_anonymous_updated($user);
                    break;
                }
                case 'xml_anonymous': {
                    blocks_cps_ues_handler::ues_xml_anonymous_updated($user);
                    break;
                }
            }
        }
    }

    public function findOrphanedGroups(ues_semester $semester) {
        global $DB;
        $semesterprefix = $this->get_semester_prefix($semester);
        $year = $semester->year;
        $name = $semester->name;

        $sql = "SELECT
            CONCAT(u.id, '-', gg.id, '-', cc.id, '-', gg.name) as uid,
            u.id AS userId,
            cc.id AS courseId,
            gg.id as groupId,
            u.username,
            cc.fullname,
            gg.name
        FROM (
            SELECT
                grp.id,
                grp.courseid,
                grp.name,
                c.fullname
            FROM (
                SELECT
                    g.name,
                    count(g.name) as gcount
                FROM {groups} g
                INNER JOIN {course} c ON g.courseid = c.id
                WHERE c.fullname like '$semesterprefix %'
                GROUP BY g.name
                HAVING gcount > 1
            ) AS dupes
            LEFT JOIN {groups} grp ON grp.name = dupes.name
            INNER JOIN {course} c ON c.id = grp.courseid
            WHERE c.fullname like '$semesterprefix %'
                AND (
                        SELECT count(id) AS memcount
                        FROM {groups_members}
                        WHERE groupid = grp.id
                    ) > 0
            ORDER BY c.fullname
            ) AS gg
            INNER JOIN {course} cc ON cc.id = gg.courseid
            INNER JOIN {groups_members} ggm ON ggm.groupid = gg.id
            INNER JOIN {user} u ON ggm.userid = u.id
            INNER JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = 50
            INNER JOIN {role_assignments} ra ON ctx.id = ra.contextid AND u.id = ra.userid
            INNER JOIN {role} r ON ra.roleid = r.id AND r.archetype = 'student'
        WHERE CONCAT(gg.courseid,gg.name) NOT IN (
            SELECT DISTINCT(CONCAT(mc.id,g.name))
            FROM {enrol_ues_sections} s
                INNER JOIN {enrol_ues_courses} c ON s.courseid = c.id
                INNER JOIN {enrol_ues_semesters} sem ON s.semesterid = sem.id
                INNER JOIN {course} mc ON mc.idnumber = s.idnumber
                INNER JOIN
                (
            SELECT
                grp.id,
                grp.courseid,
                grp.name,
                c.fullname
            FROM (
                SELECT
                    g.name,
                    count(g.name) as gcount
                FROM {groups} g
                INNER JOIN {course} c ON g.courseid = c.id
                WHERE c.fullname like '$semesterprefix %'
                GROUP BY g.name
                HAVING gcount > 1
            ) AS dupes
            LEFT JOIN {groups} grp ON grp.name = dupes.name
            INNER JOIN {course} c ON c.id = grp.courseid
            WHERE c.fullname like '$semesterprefix %'
                AND (
                        SELECT count(id) AS memcount
                        FROM {groups_members}
                        WHERE groupid = grp.id
                    ) > 0
            ORDER BY c.fullname
            ) g ON mc.id = g.courseid AND g.name = CONCAT(c.department, ' ', c.cou_number, ' ', s.sec_number)
            WHERE sem.name = '$name'
            AND sem.year = $year)
        AND gg.name IN (
            SELECT DISTINCT(g.name)
            FROM {enrol_ues_sections} s
                INNER JOIN {enrol_ues_courses} c ON s.courseid = c.id
                INNER JOIN {enrol_ues_semesters} sem ON s.semesterid = sem.id
                INNER JOIN {course} mc ON mc.idnumber = s.idnumber
                INNER JOIN
                (
            SELECT
                grp.id,
                grp.courseid,
                grp.name,
                c.fullname
            FROM (
                SELECT
                    g.name,
                    count(g.name) as gcount
                FROM {groups} g
                INNER JOIN {course} c ON g.courseid = c.id
                WHERE c.fullname like '$semesterprefix %'
                GROUP BY g.name
                HAVING gcount > 1
            ) AS dupes
            LEFT JOIN {groups} grp ON grp.name = dupes.name
            INNER JOIN {course} c ON c.id = grp.courseid
            WHERE c.fullname like '$semesterprefix %'
                AND (
                        SELECT count(id) AS memcount
                        FROM {groups_members}
                        WHERE groupid = grp.id
                    ) > 0
            ORDER BY c.fullname
            ) g ON mc.id = g.courseid AND g.name = CONCAT(c.department, ' ', c.cou_number, ' ', s.sec_number)
            WHERE sem.name = '$name'
            AND sem.year = $year)
        AND cc.visible = 1
        AND cc.shortname LIKE '$semesterprefix %';";

        return $DB->get_records_sql($sql);
    }

    /**
     * Specialized cleanup fn to unenroll users from groups
     *
     * Use cases: unenroll members of orphaned groups
     * Takes the output of @see azure_enrollment_provider::findOrphanedGroups
     * and prepares it for unenrollment.
     *
     * @todo parameterize queries for semester prefix- remove hardcoded course prefeix!!!
     * @global object $DB
     * @param object[] $groupMembers rows from
     * @see azure_enrollment_provider::findOrphanedGroups
     */
    public function unenrollGroupsUsers($groupMembers) {
        $ues        = new enrol_ues_plugin();
        foreach ($groupMembers as $user) {
            $instance   = $ues->get_instance($user->courseid);
            $ues->unenrol_user($instance, $user->userid);
        }
    }

    public function findDuplicateGroupMembers(ues_semester $semester) {
        global $DB;
        $semesterprefix = $this->get_semester_prefix($semester);

        $sql = "SELECT CONCAT (u.firstname, ' ', u.lastname) AS UserFullname
                               , u.username
                               , g.name
                               , u.id userid
                               , c.id courseid
                               , g.id
                               , c.fullname
                               , COUNT(g.name) AS groupcount
                FROM {groups_members} gm
                    INNER JOIN {groups} g ON g.id = gm.groupid
                    INNER JOIN {course} c ON g.courseid = c.id
                    INNER JOIN {user} u ON gm.userid =u.id
                WHERE c.fullname NOT LIKE CONCAT('%', u.firstname, ' ', u.lastname)
                    AND c.fullname LIKE '$semesterprefix %'
                GROUP BY gm.groupid, u.username
                HAVING groupcount > 1;";

        return $DB->get_records_sql($sql);
    }

    public function removeGroupDupes($dupes) {

        global $DB;

        foreach ($dupes as $dupe) {
            // Find all records for the current user/groupid.
            $dupeRecs = $DB->get_records('groups_members', array('groupid' => $dupe->id, 'userid' => $dupe->userid));

            // Delete from DB until only one remains.
            while (count($dupeRecs) > 1) {
                $toDelete = array_shift($dupeRecs);
                $DB->delete_records('groups_members', array('id' => $toDelete->id));
            }
        }
    }

    private function get_semester_prefix(ues_semester $semester) {
        return sprintf("%s %s", $semester->year, $semester->name);
    }

}
