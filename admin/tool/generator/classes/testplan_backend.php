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
 * Test plan generator.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the files required by JMeter.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_testplan_backend extends tool_generator_backend {

    /**
     * @var The URL to the repository of the external project.
     */
    protected static $repourl = 'https://github.com/moodlehq/moodle-performance-comparison';

    /**
     * @var Number of users depending on the selected size.
     */
    protected static $users = array(1, 30, 100, 1000, 5000, 10000);

    /**
     * @var Number of loops depending on the selected size.
     */
    protected static $loops = array(5, 5, 5, 6, 6, 7);

    /**
     * @var Rampup period depending on the selected size.
     */
    protected static $rampups = array(1, 6, 40, 100, 500, 800);

    /**
     * Gets a list of size choices supported by this backend.
     *
     * @return array List of size (int) => text description for display
     */
    public static function get_size_choices() {

        $options = array();
        for ($size = self::MIN_SIZE; $size <= self::MAX_SIZE; $size++) {
            $a = new stdClass();
            $a->users = self::$users[$size];
            $a->loops = self::$loops[$size];
            $a->rampup = self::$rampups[$size];
            $options[$size] = get_string('testplansize_' . $size, 'tool_generator', $a);
        }
        return $options;
    }

    /**
     * Getter for moodle-performance-comparison project URL.
     *
     * @return string
     */
    public static function get_repourl() {
        return self::$repourl;
    }

    /**
     * Creates the test plan file.
     *
     * @param int $courseid The target course id
     * @param int $size The test plan size
     * @return stored_file
     */
    public static function create_testplan_file($courseid, $size) {
        $jmxcontents = self::generate_test_plan($courseid, $size);

        $fs = get_file_storage();
        $filerecord = self::get_file_record('testplan', 'jmx');
        return $fs->create_file_from_string($filerecord, $jmxcontents);
    }

    /**
     * Creates the users data file.
     *
     * @param int $courseid The target course id
     * @param bool $updateuserspassword Updates the course users password to $CFG->tool_generator_users_password
     * @param int|null $size of the test plan. Used to limit the number of users exported
     *                 to match the threads in the plan. For BC, defaults to null that means all enrolled users.
     * @return stored_file
     */
    public static function create_users_file($courseid, $updateuserspassword, ?int $size = null) {
        $csvcontents = self::generate_users_file($courseid, $updateuserspassword, $size);

        $fs = get_file_storage();
        $filerecord = self::get_file_record('users', 'csv');
        return $fs->create_file_from_string($filerecord, $csvcontents);
    }

    /**
     * Generates the test plan according to the target course contents.
     *
     * @param int $targetcourseid The target course id
     * @param int $size The test plan size
     * @return string The test plan as a string
     */
    protected static function generate_test_plan($targetcourseid, $size) {
        global $CFG;

        // Getting the template.
        $template = file_get_contents(__DIR__ . '/../testplan.template.jmx');

        // Getting the course modules data.
        $coursedata = self::get_course_test_data($targetcourseid);

        // Host and path to the site.
        $urlcomponents = parse_url($CFG->wwwroot);
        if (empty($urlcomponents['path'])) {
            $urlcomponents['path'] = '';
        }

        $replacements = array(
            $CFG->version,
            self::$users[$size],
            self::$loops[$size],
            self::$rampups[$size],
            $urlcomponents['host'],
            $urlcomponents['path'],
            get_string('shortsize_' . $size, 'tool_generator'),
            $targetcourseid,
            $coursedata->pageid,
            $coursedata->forumid,
            $coursedata->forumdiscussionid,
            $coursedata->forumreplyid
        );

        $placeholders = array(
            '{{MOODLEVERSION_PLACEHOLDER}}',
            '{{USERS_PLACEHOLDER}}',
            '{{LOOPS_PLACEHOLDER}}',
            '{{RAMPUP_PLACEHOLDER}}',
            '{{HOST_PLACEHOLDER}}',
            '{{SITEPATH_PLACEHOLDER}}',
            '{{SIZE_PLACEHOLDER}}',
            '{{COURSEID_PLACEHOLDER}}',
            '{{PAGEACTIVITYID_PLACEHOLDER}}',
            '{{FORUMACTIVITYID_PLACEHOLDER}}',
            '{{FORUMDISCUSSIONID_PLACEHOLDER}}',
            '{{FORUMREPLYID_PLACEHOLDER}}'
        );

        // Fill the template with the target course values.
        return str_replace($placeholders, $replacements, $template);
    }

    /**
     * Generates the user's credentials file with all the course's users
     *
     * @param int $targetcourseid
     * @param bool $updateuserspassword Updates the course users password to $CFG->tool_generator_users_password
     * @param int|null $size of the test plan. Used to limit the number of users exported
     *                 to match the threads in the plan. For BC, defaults to null that means all enrolled users.
     * @return string The users csv file contents.
     */
    protected static function generate_users_file($targetcourseid, $updateuserspassword, ?int $size = null) {
        global $CFG;

        $coursecontext = context_course::instance($targetcourseid);

        // If requested, get the number of users (threads) to use in the plan. We only need those in the exported file.
        $planusers = self::$users[$size] ?? 0;
        $users = get_enrolled_users($coursecontext, '', 0, 'u.id, u.username, u.auth', 'u.username ASC', 0, $planusers);
        if (!$users) {
            throw new \moodle_exception('coursewithoutusers', 'tool_generator');
        }

        $lines = array();
        foreach ($users as $user) {

            // Updating password to the one set in config.php.
            if ($updateuserspassword) {
                $userauth = get_auth_plugin($user->auth);
                if (!$userauth->user_update_password($user, $CFG->tool_generator_users_password)) {
                    throw new \moodle_exception('errorpasswordupdate', 'auth');
                }
            }

            // Here we already checked that $CFG->tool_generator_users_password is not null.
            $lines[] = $user->username . ',' . $CFG->tool_generator_users_password;
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Returns a tool_generator file record
     *
     * @param string $filearea testplan or users
     * @param string $filetype The file extension jmx or csv
     * @return stdClass The file record to use when creating tool_generator files
     */
    protected static function get_file_record($filearea, $filetype) {

        $systemcontext = context_system::instance();

        $filerecord = new stdClass();
        $filerecord->contextid = $systemcontext->id;
        $filerecord->component = 'tool_generator';
        $filerecord->filearea = $filearea;
        $filerecord->itemid = 0;
        $filerecord->filepath = '/';

        // Random generated number to avoid concurrent execution problems.
        $filerecord->filename = $filearea . '_' . date('YmdHi', time()) . '_' . rand(1000, 9999) . '.' . $filetype;

        return $filerecord;
    }

    /**
     * Gets the data required to fill the test plan template with the database contents.
     *
     * @param int $targetcourseid The target course id
     * @return stdClass The ids required by the test plan
     */
    protected static function get_course_test_data($targetcourseid) {
        global $DB, $USER;

        $data = new stdClass();

        // Getting course contents info as the current user (will be an admin).
        $course = new stdClass();
        $course->id = $targetcourseid;
        $courseinfo = new course_modinfo($course, $USER->id);

        // Getting the first page module instance.
        if (!$pages = $courseinfo->get_instances_of('page')) {
            throw new \moodle_exception('error_nopageinstances', 'tool_generator');
        }
        $data->pageid = reset($pages)->id;

        // Getting the first forum module instance and it's first discussion and reply as well.
        if (!$forums = $courseinfo->get_instances_of('forum')) {
            throw new \moodle_exception('error_noforuminstances', 'tool_generator');
        }
        $forum = reset($forums);

        // Getting the first discussion (and reply).
        if (!$discussions = forum_get_discussions($forum, 'd.timemodified ASC', false, -1, 1)) {
            throw new \moodle_exception('error_noforumdiscussions', 'tool_generator');
        }
        $discussion = reset($discussions);

        $data->forumid = $forum->id;
        $data->forumdiscussionid = $discussion->discussion;
        $data->forumreplyid = $discussion->id;

        // According to the current test plan.
        return $data;
    }

    /**
     * Checks if the selected target course is ok.
     *
     * @param int|string $course
     * @param int $size
     * @return array Errors array or false if everything is ok
     */
    public static function has_selected_course_any_problem($course, $size) {
        global $DB;

        $errors = array();

        if (!is_numeric($course)) {
            if (!$course = $DB->get_field('course', 'id', array('shortname' => $course))) {
                $errors['courseid'] = get_string('error_nonexistingcourse', 'tool_generator');
                return $errors;
            }
        }

        $coursecontext = context_course::instance($course, IGNORE_MISSING);
        if (!$coursecontext) {
            $errors['courseid'] = get_string('error_nonexistingcourse', 'tool_generator');
            return $errors;
        }

        if (!$users = get_enrolled_users($coursecontext, '', 0, 'u.id')) {
            $errors['courseid'] = get_string('coursewithoutusers', 'tool_generator');
        }

        // Checks that the selected course has enough users.
        $coursesizes = tool_generator_course_backend::get_users_per_size();
        if (count($users) < self::$users[$size]) {
            $errors['size'] = get_string('notenoughusers', 'tool_generator');
        }

        if (empty($errors)) {
            return false;
        }

        return $errors;
    }
}
