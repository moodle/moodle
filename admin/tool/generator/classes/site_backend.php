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
 * tool_generator site backend.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/backend.php');

/**
 * Backend code for the site generator.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_site_backend extends tool_generator_backend {

    /**
     * @var string The course's shortname prefix.
     */
    const SHORTNAMEPREFIX = 'testcourse_';

    /**
     * @var bool If the debugging level checking was skipped.
     */
    protected $bypasscheck;

    /**
     * @var array Multidimensional array where the first level is the course size and the second the site size.
     */
    protected static $sitecourses = array(
        array(2, 8, 64, 256, 1024, 4096),
        array(1, 4, 8, 16, 32, 64),
        array(0, 0, 1, 4, 8, 16),
        array(0, 0, 0, 1, 0, 0),
        array(0, 0, 0, 0, 1, 0),
        array(0, 0, 0, 0, 0, 1)
    );

    /**
     * Constructs object ready to make the site.
     *
     * @param int $size Size as numeric index
     * @param bool $bypasscheck If debugging level checking was skipped.
     * @param bool $fixeddataset To use fixed or random data
     * @param int|bool $filesizelimit The max number of bytes for a generated file
     * @param bool $progress True if progress information should be displayed
     * @return int Course id
     */
    public function __construct($size, $bypasscheck, $fixeddataset = false, $filesizelimit = false, $progress = true) {

        // Set parameters.
        $this->bypasscheck = $bypasscheck;

        parent::__construct($size, $fixeddataset, $filesizelimit, $progress);
    }

    /**
     * Gets a list of size choices supported by this backend.
     *
     * @return array List of size (int) => text description for display
     */
    public static function get_size_choices() {
        $options = array();
        for ($size = self::MIN_SIZE; $size <= self::MAX_SIZE; $size++) {
            $options[$size] = get_string('sitesize_' . $size, 'tool_generator');
        }
        return $options;
    }

    /**
     * Runs the entire 'make' process.
     *
     * @return int Course id
     */
    public function make() {
        global $DB, $CFG;

        raise_memory_limit(MEMORY_EXTRA);

        if ($this->progress && !CLI_SCRIPT) {
            echo html_writer::start_tag('ul');
        }

        $entirestart = microtime(true);

        // Create courses.
        $prevchdir = getcwd();
        chdir($CFG->dirroot);
        $ncourse = self::get_last_testcourse_id();
        foreach (self::$sitecourses as $coursesize => $ncourses) {
            for ($i = 1; $i <= $ncourses[$this->size]; $i++) {
                // Non language-dependant shortname.
                $ncourse++;
                $this->run_create_course(self::SHORTNAMEPREFIX . $ncourse, $coursesize);
            }
        }
        chdir($prevchdir);

        // Store last course id to return it (will be the bigger one).
        $lastcourseid = $DB->get_field('course', 'id', array('shortname' => self::SHORTNAMEPREFIX . $ncourse));

        // Log total time.
        $this->log('sitecompleted', round(microtime(true) - $entirestart, 1));

        if ($this->progress && !CLI_SCRIPT) {
            echo html_writer::end_tag('ul');
        }

        return $lastcourseid;
    }

    /**
     * Creates a course with the specified shortname, coursesize and the provided maketestsite options.
     *
     * @param string $shortname The course shortname
     * @param int $coursesize One of the possible course sizes.
     * @return void
     */
    protected function run_create_course($shortname, $coursesize) {

        // We are in $CFG->dirroot.
        $command = 'php admin/tool/generator/cli/maketestcourse.php';

        $options = array(
            '--shortname="' . $shortname . '"',
            '--size="' . get_string('shortsize_' . $coursesize, 'tool_generator') . '"'
        );

        if (!$this->progress) {
            $options[] = '--quiet';
        }

        // Extend options.
        $optionstoextend = array(
            'fixeddataset' => 'fixeddataset',
            'bypasscheck' => 'bypasscheck',
        );

        if ($this->filesizelimit) {
            $options[] = '--filesizelimit="' . $this->filesizelimit . '"';
        }

        // Getting an options string.
        foreach ($optionstoextend as $attribute => $option) {
            if (!empty($this->{$attribute})) {
                $options[] = '--' . $option;
            }
        }
        $options = implode(' ', $options);
        if ($this->progress) {
            system($command . ' ' . $options, $exitcode);
        } else {
            passthru($command . ' ' . $options, $exitcode);
        }

        if ($exitcode != 0) {
            exit($exitcode);
        }
    }

    /**
     * Obtains the last unique sufix (numeric) using the test course prefix.
     *
     * @return int The last generated numeric value.
     */
    protected static function get_last_testcourse_id() {
        global $DB;

        $params = array();
        $params['shortnameprefix'] = $DB->sql_like_escape(self::SHORTNAMEPREFIX) . '%';
        $like = $DB->sql_like('shortname', ':shortnameprefix');

        if (!$testcourses = $DB->get_records_select('course', $like, $params, '', 'shortname')) {
            return 0;
        }
        // SQL order by is not appropiate here as is ordering strings.
        $shortnames = array_keys($testcourses);
        collatorlib::asort($shortnames, collatorlib::SORT_NATURAL);
        $shortnames = array_reverse($shortnames);

        // They come ordered by shortname DESC, so non-numeric values will be the first ones.
        $prefixnchars = strlen(self::SHORTNAMEPREFIX);
        foreach ($shortnames as $shortname) {
            $sufix = substr($shortname, $prefixnchars);
            if (preg_match('/^[\d]+$/', $sufix)) {
                return $sufix;
            }
        }
        // If all sufixes are not numeric this is the first make test site run.
        return 0;
    }

}
