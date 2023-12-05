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
 * tool_generator course backend code.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Backend code for the 'make large course' tool.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_course_backend extends tool_generator_backend {
    /**
     * @var array Number of sections in course
     */
    private static $paramsections = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of assignments in course
     */
    private static $paramassignments = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of Page activities in course
     */
    private static $parampages = array(1, 50, 200, 1000, 5000, 10000);
    /**
     * @var array Number of students enrolled in course
     */
    private static $paramusers = array(1, 100, 1000, 10000, 50000, 100000);
    /**
     * Total size of small files: 1KB, 1MB, 10MB, 100MB, 1GB, 2GB.
     *
     * @var array Number of small files created in a single file activity
     */
    private static $paramsmallfilecount = array(1, 64, 128, 1024, 16384, 32768);
    /**
     * @var array Size of small files (to make the totals into nice numbers)
     */
    private static $paramsmallfilesize = array(1024, 16384, 81920, 102400, 65536, 65536);
    /**
     * Total size of big files: 8KB, 8MB, 80MB, 800MB, 8GB, 16GB.
     *
     * @var array Number of big files created as individual file activities
     */
    private static $parambigfilecount = array(1, 2, 5, 10, 10, 10);
    /**
     * @var array Size of each large file
     */
    private static $parambigfilesize = array(8192, 4194304, 16777216, 83886080,
            858993459, 1717986918);
    /**
     * @var array Number of forum discussions
     */
    private static $paramforumdiscussions = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of forum posts per discussion
     */
    private static $paramforumposts = array(2, 2, 5, 10, 10, 10);

    /**
     * @var array Number of assignments in course
     */
    private static $paramactivities = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var string Course shortname
     */
    private $shortname;

    /**
     * @var string Course fullname.
     */
    private $fullname = "";

    /**
     * @var string Course summary.
     */
    private $summary = "";

    /**
     * @var string Course summary format, defaults to FORMAT_HTML.
     */
    private $summaryformat = FORMAT_HTML;

    /**
     * @var testing_data_generator Data generator
     */
    protected $generator;

    /**
     * @var stdClass Course object
     */
    private $course;

    /**
     * @var array Array from test user number (1...N) to userid in database
     */
    private $userids;

    /**
     * @var array $additionalmodules
     */
    private $additionalmodules;
    /**
     * Constructs object ready to create course.
     *
     * @param string $shortname Course shortname
     * @param int $size Size as numeric index
     * @param bool $fixeddataset To use fixed or random data
     * @param int|bool $filesizelimit The max number of bytes for a generated file
     * @param bool $progress True if progress information should be displayed
     * @param array $additionalmodules potential additional modules to be added (quiz, bigbluebutton...)
     */
    public function __construct(
        $shortname,
        $size,
        $fixeddataset = false,
        $filesizelimit = false,
        $progress = true,
        $fullname = null,
        $summary = null,
        $summaryformat = FORMAT_HTML,
        $additionalmodules = []
    ) {

        // Set parameters.
        $this->shortname = $shortname;

        // We can't allow fullname to be set to an empty string.
        if (empty($fullname)) {
            $this->fullname = get_string(
                'fullname',
                'tool_generator',
                array(
                    'size' => get_string('shortsize_' . $size, 'tool_generator')
                )
            );
        } else {
            $this->fullname = $fullname;
        }

        // Summary, on the other hand, should be empty-able.
        if (!is_null($summary)) {
            $this->summary = $summary;
            $this->summaryformat = $summaryformat;
        }
        $this->additionalmodules = $additionalmodules;
        parent::__construct($size, $fixeddataset, $filesizelimit, $progress);
    }

    /**
     * Returns the relation between users and course sizes.
     *
     * @return array
     */
    public static function get_users_per_size() {
        return self::$paramusers;
    }

    /**
     * Gets a list of size choices supported by this backend.
     *
     * @return array List of size (int) => text description for display
     */
    public static function get_size_choices() {
        $options = array();
        for ($size = self::MIN_SIZE; $size <= self::MAX_SIZE; $size++) {
            $options[$size] = get_string('coursesize_' . $size, 'tool_generator');
        }
        return $options;
    }

    /**
     * Checks that a shortname is available (unused).
     *
     * @param string $shortname Proposed course shortname
     * @return string An error message if the name is unavailable or '' if OK
     */
    public static function check_shortname_available($shortname) {
        global $DB;
        $fullname = $DB->get_field('course', 'fullname',
                array('shortname' => $shortname), IGNORE_MISSING);
        if ($fullname !== false) {
            // I wanted to throw an exception here but it is not possible to
            // use strings from moodle.php in exceptions, and I didn't want
            // to duplicate the string in tool_generator, so I changed this to
            // not use exceptions.
            return get_string('shortnametaken', 'moodle', $fullname);
        }
        return '';
    }

    /**
     * Runs the entire 'make' process.
     *
     * @return int Course id
     */
    public function make() {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

        raise_memory_limit(MEMORY_EXTRA);

        if ($this->progress && !CLI_SCRIPT) {
            echo html_writer::start_tag('ul');
        }

        $entirestart = microtime(true);

        // Get generator.
        $this->generator = phpunit_util::get_data_generator();

        // Make course.
        $this->course = $this->create_course();

        $this->create_assignments();
        $this->create_pages();
        $this->create_small_files();
        $this->create_big_files();

        // Create users as late as possible to reduce regarding in the gradebook.
        $this->create_users();
        $this->create_forum();

        // Let plugins hook into user settings navigation.
        $pluginsfunction = get_plugins_with_function('course_backend_generator_create_activity');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginname => $pluginfunction) {
                if (in_array($pluginname, $this->additionalmodules)) {
                    $pluginfunction($this, $this->generator, $this->course->id, self::$paramactivities[$this->size]);
                }
            }
        }

        // We are checking 'enroladminnewcourse' setting to decide to enrol admins or not.
        if (!empty($CFG->creatornewroleid) && !empty($CFG->enroladminnewcourse) && is_siteadmin($USER->id)) {
            // Deal with course creators - enrol them internally with default role.
            enrol_try_internal_enrol($this->course->id, $USER->id, $CFG->creatornewroleid);
        }

        // Log total time.
        $this->log('coursecompleted', round(microtime(true) - $entirestart, 1));

        if ($this->progress && !CLI_SCRIPT) {
            echo html_writer::end_tag('ul');
        }

        return $this->course->id;
    }

    /**
     * Creates the actual course.
     *
     * @return stdClass Course record
     */
    private function create_course() {
        $this->log('createcourse', $this->shortname);
        $courserecord = array(
            'shortname' => $this->shortname,
            'fullname' => $this->fullname,
            'numsections' => self::$paramsections[$this->size],
            'startdate' => usergetmidnight(time())
        );
        if (strlen($this->summary) > 0) {
            $courserecord['summary'] = $this->summary;
            $courserecord['summary_format'] = $this->summaryformat;
        }

        return $this->generator->create_course($courserecord, array('createsections' => true));
    }

    /**
     * Creates a number of user accounts and enrols them on the course.
     * Note: Existing user accounts that were created by this system are
     * reused if available.
     */
    private function create_users() {
        global $DB;

        // Work out total number of users.
        $count = self::$paramusers[$this->size];

        // Get existing users in order. We will 'fill up holes' in this up to
        // the required number.
        $this->log('checkaccounts', $count);
        $nextnumber = 1;
        $rs = $DB->get_recordset_select('user', $DB->sql_like('username', '?'),
                array('tool_generator_%'), 'username', 'id, username');
        foreach ($rs as $rec) {
            // Extract number from username.
            $matches = array();
            if (!preg_match('~^tool_generator_([0-9]{6})$~', $rec->username, $matches)) {
                continue;
            }
            $number = (int)$matches[1];

            // Create missing users in range up to this.
            if ($number != $nextnumber) {
                $this->create_user_accounts($nextnumber, min($number - 1, $count));
            } else {
                $this->userids[$number] = (int)$rec->id;
            }

            // Stop if we've got enough users.
            $nextnumber = $number + 1;
            if ($number >= $count) {
                break;
            }
        }
        $rs->close();

        // Create users from end of existing range.
        if ($nextnumber <= $count) {
            $this->create_user_accounts($nextnumber, $count);
        }

        // Assign all users to course.
        $this->log('enrol', $count, true);

        $enrolplugin = enrol_get_plugin('manual');
        $instances = enrol_get_instances($this->course->id, true);
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                break;
            }
        }
        if ($instance->enrol !== 'manual') {
            throw new coding_exception('No manual enrol plugin in course');
        }
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);

        for ($number = 1; $number <= $count; $number++) {
            // Enrol user.
            $enrolplugin->enrol_user($instance, $this->userids[$number], $role->id);
            $this->dot($number, $count);
        }

        // Sets the pointer at the beginning to be aware of the users we use.
        reset($this->userids);

        $this->end_log();
    }

    /**
     * Creates user accounts with a numeric range.
     *
     * @param int $first Number of first user
     * @param int $last Number of last user
     */
    private function create_user_accounts($first, $last) {
        global $CFG;

        $this->log('createaccounts', (object)array('from' => $first, 'to' => $last), true);
        $count = $last - $first + 1;
        $done = 0;
        for ($number = $first; $number <= $last; $number++, $done++) {
            // Work out username with 6-digit number.
            $textnumber = (string)$number;
            while (strlen($textnumber) < 6) {
                $textnumber = '0' . $textnumber;
            }
            $username = 'tool_generator_' . $textnumber;

            // Create user account.
            $record = array('username' => $username, 'idnumber' => $number);

            // We add a user password if it has been specified.
            if (!empty($CFG->tool_generator_users_password)) {
                $record['password'] = $CFG->tool_generator_users_password;
            }

            $user = $this->generator->create_user($record);
            $this->userids[$number] = (int)$user->id;
            $this->dot($done, $count);
        }
        $this->end_log();
    }

    /**
     * Creates a number of Assignment activities.
     */
    private function create_assignments() {
        // Set up generator.
        $assigngenerator = $this->generator->get_plugin_generator('mod_assign');

        // Create assignments.
        $number = self::$paramassignments[$this->size];
        $this->log('createassignments', $number, true);
        for ($i = 0; $i < $number; $i++) {
            $record = array('course' => $this->course);
            $options = array('section' => $this->get_target_section());
            $assigngenerator->create_instance($record, $options);
            $this->dot($i, $number);
        }

        $this->end_log();
    }

    /**
     * Creates a number of Page activities.
     */
    private function create_pages() {
        // Set up generator.
        $pagegenerator = $this->generator->get_plugin_generator('mod_page');

        // Create pages.
        $number = self::$parampages[$this->size];
        $this->log('createpages', $number, true);
        for ($i = 0; $i < $number; $i++) {
            $record = array('course' => $this->course);
            $options = array('section' => $this->get_target_section());
            $pagegenerator->create_instance($record, $options);
            $this->dot($i, $number);
        }

        $this->end_log();
    }

    /**
     * Creates one resource activity with a lot of small files.
     */
    private function create_small_files() {
        $count = self::$paramsmallfilecount[$this->size];
        $this->log('createsmallfiles', $count, true);

        // Create resource with default textfile only.
        $resourcegenerator = $this->generator->get_plugin_generator('mod_resource');
        $record = array('course' => $this->course,
                'name' => get_string('smallfiles', 'tool_generator'));
        $options = array('section' => 0);
        $resource = $resourcegenerator->create_instance($record, $options);

        // Add files.
        $fs = get_file_storage();
        $context = context_module::instance($resource->cmid);
        $filerecord = array('component' => 'mod_resource', 'filearea' => 'content',
                'contextid' => $context->id, 'itemid' => 0, 'filepath' => '/');
        for ($i = 0; $i < $count; $i++) {
            $filerecord['filename'] = 'smallfile' . $i . '.dat';

            // Generate random binary data (different for each file so it
            // doesn't compress unrealistically).
            $data = random_bytes($this->limit_filesize(self::$paramsmallfilesize[$this->size]));

            $fs->create_file_from_string($filerecord, $data);
            $this->dot($i, $count);
        }

        $this->end_log();
    }

    /**
     * Creates a number of resource activities with one big file each.
     */
    private function create_big_files() {
        // Work out how many files and how many blocks to use (up to 64KB).
        $count = self::$parambigfilecount[$this->size];
        $filesize = $this->limit_filesize(self::$parambigfilesize[$this->size]);
        $blocks = ceil($filesize / 65536);
        $blocksize = floor($filesize / $blocks);

        $this->log('createbigfiles', $count, true);

        // Prepare temp area.
        $tempfolder = make_temp_directory('tool_generator');
        $tempfile = $tempfolder . '/' . rand();

        // Create resources and files.
        $fs = get_file_storage();
        $resourcegenerator = $this->generator->get_plugin_generator('mod_resource');
        for ($i = 0; $i < $count; $i++) {
            // Create resource.
            $record = array('course' => $this->course,
                    'name' => get_string('bigfile', 'tool_generator', $i));
            $options = array('section' => $this->get_target_section());
            $resource = $resourcegenerator->create_instance($record, $options);

            // Write file.
            $handle = fopen($tempfile, 'w');
            if (!$handle) {
                throw new coding_exception('Failed to open temporary file');
            }
            for ($j = 0; $j < $blocks; $j++) {
                $data = random_bytes($blocksize);
                fwrite($handle, $data);
                $this->dot($i * $blocks + $j, $count * $blocks);
            }
            fclose($handle);

            // Add file.
            $context = context_module::instance($resource->cmid);
            $filerecord = array('component' => 'mod_resource', 'filearea' => 'content',
                    'contextid' => $context->id, 'itemid' => 0, 'filepath' => '/',
                    'filename' => 'bigfile' . $i . '.dat');
            $fs->create_file_from_pathname($filerecord, $tempfile);
        }

        unlink($tempfile);
        $this->end_log();
    }

    /**
     * Creates one forum activity with a bunch of posts.
     */
    private function create_forum() {
        global $DB;

        $discussions = self::$paramforumdiscussions[$this->size];
        $posts = self::$paramforumposts[$this->size];
        $totalposts = $discussions * $posts;

        $this->log('createforum', $totalposts, true);

        // Create empty forum.
        $forumgenerator = $this->generator->get_plugin_generator('mod_forum');
        $record = array('course' => $this->course,
                'name' => get_string('pluginname', 'forum'));
        $options = array('section' => 0);
        $forum = $forumgenerator->create_instance($record, $options);

        // Add discussions and posts.
        $sofar = 0;
        for ($i = 0; $i < $discussions; $i++) {
            $record = array('forum' => $forum->id, 'course' => $this->course->id,
                    'userid' => $this->get_target_user());
            $discussion = $forumgenerator->create_discussion($record);
            $parentid = $DB->get_field('forum_posts', 'id', array('discussion' => $discussion->id), MUST_EXIST);
            $sofar++;
            for ($j = 0; $j < $posts - 1; $j++, $sofar++) {
                $record = array('discussion' => $discussion->id,
                        'userid' => $this->get_target_user(), 'parent' => $parentid);
                $forumgenerator->create_post($record);
                $this->dot($sofar, $totalposts);
            }
        }

        $this->end_log();
    }

    /**
     * Gets a section number.
     *
     * Depends on $this->fixeddataset.
     *
     * @return int A section number from 1 to the number of sections
     */
    public function get_target_section() {

        if (!$this->fixeddataset) {
            $key = rand(1, self::$paramsections[$this->size]);
        } else {
            // Using section 1.
            $key = 1;
        }

        return $key;
    }

    /**
     * Gets a user id.
     *
     * Depends on $this->fixeddataset.
     *
     * @return int A user id for a random created user
     */
    private function get_target_user() {

        if (!$this->fixeddataset) {
            $userid = $this->userids[rand(1, self::$paramusers[$this->size])];
        } else if ($userid = current($this->userids)) {
            // Moving pointer to the next user.
            next($this->userids);
        } else {
            // Returning to the beginning if we reached the end.
            $userid = reset($this->userids);
        }

        return $userid;
    }

    /**
     * Restricts the binary file size if necessary
     *
     * @param int $length The total length
     * @return int The limited length if a limit was specified.
     */
    private function limit_filesize($length) {

        // Limit to $this->filesizelimit.
        if (is_numeric($this->filesizelimit) && $length > $this->filesizelimit) {
            $length = floor($this->filesizelimit);
        }

        return $length;
    }
}
