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
 * PHPUnit data generator class
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Data generator for unit tests
 */
class phpunit_data_generator {
    protected $usercounter = 0;
    protected $categorycount = 0;
    protected $coursecount = 0;
    protected $blockcount = 0;
    protected $modulecount = 0;
    protected $scalecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->usercounter = 0;
        $this->categorycount = 0;
        $this->coursecount = 0;
        $this->blockcount = 0;
        $this->modulecount = 0;
        $this->scalecount = 0;
    }

    /**
     * Create a test user
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass user record
     */
    public function create_user($record=null, array $options=null) {
        global $DB, $CFG;

        $this->usercounter++;
        $i = $this->usercounter;

        $record = (array)$record;

        if (!isset($record['auth'])) {
            $record['auth'] = 'manual';
        }

        if (!isset($record['firstname'])) {
            $record['firstname'] = 'Firstname'.$i;
        }

        if (!isset($record['lastname'])) {
            $record['lastname'] = 'Lastname'.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['username'])) {
            $record['username'] = 'username'.$i;
        }

        if (!isset($record['password'])) {
            $record['password'] = 'lala';
        }

        if (!isset($record['email'])) {
            $record['email'] = $record['username'].'@example.com';
        }

        if (!isset($record['confirmed'])) {
            $record['confirmed'] = 1;
        }

        if (!isset($record['mnethostid'])) {
            $record['mnethostid'] = $CFG->mnet_localhost_id;
        }

        if (!isset($record['lang'])) {
            $record['lang'] = 'en';
        }

        if (!isset($record['maildisplay'])) {
            $record['maildisplay'] = 1;
        }

        if (!isset($record['deleted'])) {
            $record['deleted'] = 0;
        }

        $record['timecreated'] = time();
        $record['timemodified'] = $record['timecreated'];
        $record['lastip'] = '0.0.0.0';

        $record['password'] = hash_internal_user_password($record['password']);

        if ($record['deleted']) {
            $delname = $record['email'].'.'.time();
            while ($DB->record_exists('user', array('username'=>$delname))) {
                $delname++;
            }
            $record['idnumber'] = '';
            $record['email']    = md5($record['username']);
            $record['username'] = $delname;
        }

        $userid = $DB->insert_record('user', $record);
        if (!$record['deleted']) {
            context_user::instance($userid);
        }

        return $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    }

    /**
     * Create a test course category
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass course category record
     */
    function create_category($record=null, array $options=null) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $this->categorycount++;
        $i = $this->categorycount;

        $record = (array)$record;

        if (!isset($record['name'])) {
            $record['name'] = 'Course category '.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['description'])) {
            $record['description'] = 'Test course category '.$i;
        }

        if (!isset($record['descriptionformat'])) {
            $record['description'] = FORMAT_MOODLE;
        }

        if (!isset($record['parent'])) {
            $record['descriptionformat'] = 0;
        }

        if ($record['parent'] == 0) {
            $parent = new stdClass();
            $parent->path = '';
            $parent->depth = 0;
        } else {
            $parent = $DB->get_record('course_categories', array('id'=>$record['parent']), '*', MUST_EXIST);
        }
        $record['depth'] = $parent->depth+1;

        $record['sortorder'] = 0;
        $record['timemodified'] = time();
        $record['timecreated'] = $record['timemodified'];

        $catid = $DB->insert_record('course_categories', $record);
        $path = $parent->path . '/' . $catid;
        $DB->set_field('course_categories', 'path', $path, array('id'=>$catid));
        context_coursecat::instance($catid);

        fix_course_sortorder();

        return $DB->get_record('course_categories', array('id'=>$catid), '*', MUST_EXIST);
    }

    /**
     * Create a test course
     * @param array|stdClass $record
     * @param array $options with keys:
     *      'createsections'=>bool precreate all sections
     * @return stdClass course record
     */
    function create_course($record=null, array $options=null) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $this->coursecount++;
        $i = $this->coursecount;

        $record = (array)$record;

        if (!isset($record['fullname'])) {
            $record['fullname'] = 'Test course '.$i;
        }

        if (!isset($record['shortname'])) {
            $record['shortname'] = 'tc_'.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['format'])) {
            $record['format'] = 'topics';
        }

        if (!isset($record['newsitems'])) {
            $record['newsitems'] = 0;
        }

        if (!isset($record['numsections'])) {
            $record['numsections'] = 5;
        }

        if (!isset($record['description'])) {
            $record['description'] = 'Test course '.$i;
        }

        if (!isset($record['descriptionformat'])) {
            $record['description'] = FORMAT_MOODLE;
        }

        if (!isset($record['category'])) {
            $record['category'] = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        }

        $course = create_course((object)$record);
        context_course::instance($course->id);

        if (!empty($options['createsections'])) {
            for($i=1; $i<$record['numsections']; $i++) {
                self::create_course_section(array('course'=>$course->id, 'section'=>$i));
            }
        }

        return $course;
    }

    /**
     * Create course section if does not exist yet
     * @param mixed $record
     * @param array|null $options
     * @return stdClass
     * @throws coding_exception
     */
    public function create_course_section($record = null, array $options = null) {
        global $DB;

        $record = (array)$record;

        if (empty($record['course'])) {
            throw new coding_exception('course must be present in phpunit_util::create_course_section() $record');
        }

        if (!isset($record['section'])) {
            throw new coding_exception('section must be present in phpunit_util::create_course_section() $record');
        }

        if (!isset($record['name'])) {
            $record['name'] = '';
        }

        if (!isset($record['summary'])) {
            $record['summary'] = '';
        }

        if (!isset($record['summaryformat'])) {
            $record['summaryformat'] = FORMAT_MOODLE;
        }

        if ($section = $DB->get_record('course_sections', array('course'=>$record['course'], 'section'=>$record['section']))) {
            return $section;
        }

        $section = new stdClass();
        $section->course        = $record['course'];
        $section->section       = $record['section'];
        $section->name          = $record['name'];
        $section->summary       = $record['summary'];
        $section->summaryformat = $record['summaryformat'];
        $id = $DB->insert_record('course_sections', $section);

        return $DB->get_record('course_sections', array('id'=>$id));
    }

    /**
     * Create a test block
     * @param string $blockname
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass block instance record
     */
    public function create_block($blockname, $record=null, array $options=null) {
        global $DB;

        $this->blockcount++;
        $i = $this->blockcount;

        $record = (array)$record;

        $record['blockname'] = $blockname;

        //TODO: use block callbacks

        if (!isset($record['parentcontextid'])) {
            $record['parentcontextid'] = context_system::instance()->id;
        }

        if (!isset($record['showinsubcontexts'])) {
            $record['showinsubcontexts'] = 1;
        }

        if (!isset($record['pagetypepattern'])) {
            $record['pagetypepattern'] = '';
        }

        if (!isset($record['subpagepattern'])) {
            $record['subpagepattern'] = '';
        }

        if (!isset($record['defaultweight'])) {
            $record['defaultweight'] = '';
        }

        $biid = $DB->insert_record('block_instances', $record);
        context_block::instance($biid);

        return $DB->get_record('block_instances', array('id'=>$biid), '*', MUST_EXIST);
    }

    /**
     * Create a test module
     * @param string $modulename
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    public function create_module($modulename, $record=null, array $options=null) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $this->modulecount++;
        $i = $this->modulecount;

        $record = (array)$record;
        $options = (array)$options;

        if (!isset($record['name'])) {
            $record['name'] = get_string('pluginname', $modulename).' '.$i;
        }

        if (!isset($record['intro'])) {
            $record['intro'] = 'Test module '.$i;
        }

        if (!isset($record['introformat'])) {
            $record['introformat'] = FORMAT_MOODLE;
        }

        if (!isset($options['section'])) {
            $options['section'] = 1;
        }

        //TODO: use module callbacks

        if ($modulename === 'page') {
            if (!isset($record['content'])) {
                $record['content'] = 'Test page content';
            }
            if (!isset($record['contentformat'])) {
                $record['contentformat'] = FORMAT_MOODLE;
            }

        } else {
            error('TODO: only mod_page is supported in data generator for now');
        }

        $id = $DB->insert_record($modulename, $record);

        $cm = new stdClass();
        $cm->course   = $record['course'];
        $cm->module   = $DB->get_field('modules', 'id', array('name'=>$modulename));
        $cm->section  = $options['section'];
        $cm->instance = $id;
        $cm->id = $DB->insert_record('course_modules', $cm);

        $cm->coursemodule = $cm->id;
        add_mod_to_section($cm);

        context_module::instance($cm->id);

        $instance = $DB->get_record($modulename, array('id'=>$id), '*', MUST_EXIST);
        $instance->cmid = $cm->id;

        return $instance;
    }

    /**
     * Create a test scale
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass block instance record
     */
    public function create_scale($record=null, array $options=null) {
        global $DB;

        $this->scalecount++;
        $i = $this->scalecount;

        $record = (array)$record;

        if (!isset($record['name'])) {
            $record['name'] = 'Test scale '.$i;
        }

        if (!isset($record['scale'])) {
            $record['scale'] = 'A,B,C,D,F';
        }

        if (!isset($record['courseid'])) {
            $record['courseid'] = 0;
        }

        if (!isset($record['userid'])) {
            $record['userid'] = 0;
        }

        if (!isset($record['description'])) {
            $record['description'] = 'Test scale description '.$i;
        }

        if (!isset($record['descriptionformat'])) {
            $record['descriptionformat'] = FORMAT_MOODLE;
        }

        $record['timemodified'] = time();

        if (isset($record['id'])) {
            $DB->import_record('scale', $record);
            $DB->get_manager()->reset_sequence('scale');
            $id = $record['id'];
        } else {
            $id = $DB->insert_record('scale', $record);
        }

        return $DB->get_record('scale', array('id'=>$id), '*', MUST_EXIST);
    }
}
