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
 * Defines Moodle 1.9 backup conversion handlers
 *
 * Handlers are classes responsible for the actual conversion work. Their logic
 * is similar to the functionality provided by steps in plan based restore process.
 *
 * @package    backup-convert
 * @subpackage moodle1
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/xml/xml_writer.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/file_xml_output.class.php');

/**
 * Handlers factory class
 */
abstract class moodle1_handlers_factory {

    /**
     * @param moodle1_converter the converter requesting the converters
     * @return list of all available conversion handlers
     */
    public static function get_handlers(moodle1_converter $converter) {

        $handlers = array();
        $handlers[] = new moodle1_root_handler($converter);
        $handlers[] = new moodle1_info_handler($converter);
        $handlers[] = new moodle1_course_header_handler($converter);

        $handlers = array_merge($handlers, self::get_plugin_handlers('mod', $converter));
        $handlers = array_merge($handlers, self::get_plugin_handlers('block', $converter));

        // make sure that all handlers have expected class
        foreach ($handlers as $handler) {
            if (!$handler instanceof moodle1_handler) {
                throw new convert_exception('wrong_handler_class', get_class($handler));
            }
        }

        return $handlers;
    }

    /// public API ends here ///////////////////////////////////////////////////

    /**
     * Runs through all plugins of a specific type and instantiates their handlers
     *
     * @todo ask mod's subplugins
     * @param string $type the plugin type
     * @param moodle1_converter $converter the converter requesting the handler
     * @throws convert_exception
     * @return array of {@link moodle1_handler} instances
     */
    protected static function get_plugin_handlers($type, moodle1_converter $converter) {
        global $CFG;

        $handlers = array();
        $plugins = get_plugin_list($type);
        foreach ($plugins as $name => $dir) {
            $handlerfile  = $dir . '/backup/moodle1/lib.php';
            $handlerclass = "moodle1_{$type}_{$name}_handler";
            if (!file_exists($handlerfile)) {
                continue;
            }
            require_once($handlerfile);

            if (!class_exists($handlerclass)) {
                throw new convert_exception('missing_handler_class', $handlerclass);
            }
            $handlers[] = new $handlerclass($converter, $type, $name);
        }
        return $handlers;
    }
}


/**
 * Base backup conversion handler
 */
abstract class moodle1_handler {

    /** @var moodle1_converter */
    protected $converter;

    /**
     * @param moodle1_converter $converter the converter that requires us
     */
    public function __construct(moodle1_converter $converter) {
        $this->converter = $converter;
    }

    /**
     * @return moodle1_converter the converter that required this handler
     */
    public function get_converter() {
        return $this->converter;
    }
}


/**
 * Base backup conversion handler that generates an XML file
 */
abstract class moodle1_xml_handler extends moodle1_handler {

    /** @var null|string the name of file we are writing to */
    protected $xmlfilename;

    /** @var null|xml_writer */
    protected $xmlwriter;

    /**
     * Opens the XML writer - after calling, one is free to use $xmlwriter
     *
     * @param string $filename XML file name to write into
     * @return void
     */
    public function open_xml_writer($filename) {

        if (!is_null($this->xmlfilename) and $filename !== $this->xmlfilename) {
            throw new convert_exception('xml_writer_already_opened_for_other_file', $this->xmlfilename);
        }

        if (!$this->xmlwriter instanceof xml_writer) {
            $this->xmlfilename = $filename;
            $fullpath  = $this->converter->get_workdir_path() . '/' . $this->xmlfilename;
            $directory = pathinfo($fullpath, PATHINFO_DIRNAME);

            if (!check_dir_exists($directory)) {
                throw new convert_exception('unable_create_target_directory', $directory);
            }
            $this->xmlwriter = new xml_writer(new file_xml_output($fullpath));
            $this->xmlwriter->start();
        }
    }

    /**
     * Close the XML writer
     *
     * At the moment, the caller must close all tags before calling
     *
     * @return void
     */
    public function close_xml_writer() {
        if ($this->xmlwriter instanceof xml_writer) {
            $this->xmlwriter->stop();
        }
        unset($this->xmlwriter);
        $this->xmlwriter = null;
        $this->xmlfilename = null;
    }

    /**
     * Dumps the data into the XML file
     */
    public function write_xml(array $data) {

        foreach ($data as $name => $value) {
            $this->xmlwriter->full_tag($name, $value);
        }
    }
}


/**
 * Shared base class for activity modules and blocks handlers
 */
abstract class moodle1_plugin_handler extends moodle1_xml_handler {

    /** @var string */
    protected $plugintype;

    /** @var string */
    protected $pluginname;

    /**
     * @param moodle1_converter $converter the converter that requires us
     * @param string plugintype
     * @param string pluginname
     */
    public function __construct(moodle1_converter $converter, $plugintype, $pluginname) {

        parent::__construct($converter);
        $this->plugintype = $plugintype;
        $this->pluginname = $pluginname;
    }
}


/**
 * Base class for activity module handlers
 */
abstract class moodle1_mod_handler extends moodle1_plugin_handler {

    /** @var int module id */
    protected $moduleid;

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return "activities/{$this->pluginname}_{$this->moduleid}/module.xml";
    }
}


/**
 * Base class for activity module handlers
 */
abstract class moodle1_block_handler extends moodle1_plugin_handler {

}


/**
 * Process the root element of the backup file
 */
class moodle1_root_handler extends moodle1_handler {

    public function get_paths() {
        return array(new convert_path('root_element', '/MOODLE_BACKUP'));
    }

    public function process_root_element($data) {
    }

    /**
     * This is executed at the very start of the moodle.xml parsing
     */
    public function on_root_element_start() {
        $this->converter->create_backup_ids_temp_table();
    }

    /**
     * This is executed at the end of the moodle.xml parsing
     */
    public function on_root_element_end() {
        $this->converter->drop_backup_ids_temp_table();
    }
}


/**
 * Handles the conversion of /MOODLE_BACKUP/INFO paths
 */
class moodle1_info_handler extends moodle1_handler {

    public function get_paths() {
        return array(
            new convert_path(
                'info', '/MOODLE_BACKUP/INFO',
                array(
                    'newfields' => array(
                        'mnet_remoteusers' => 0,
                    ),
                )
            ),
            new convert_path('info_details', '/MOODLE_BACKUP/INFO/DETAILS'),
            new convert_path('info_details_mod', '/MOODLE_BACKUP/INFO/DETAILS/MOD'),
            new convert_path('info_details_mod_instance', '/MOODLE_BACKUP/INFO/DETAILS/MOD/INSTANCES/INSTANCE'),
        );
    }

    public function process_info($data) {
    }

    public function process_info_details($data) {
    }

    public function process_info_details_mod($data) {
    }

    public function process_info_details_mod_instance($data) {
    }
}


/**
 * Handles the conversion of /MOODLE_BACKUP/COURSE/HEADER paths
 */
class moodle1_course_header_handler extends moodle1_xml_handler {

    /** @var array we need to merge course information because it is dispatched twice */
    protected $course = array();

    /** @var array we need to merge course information because it is dispatched twice */
    protected $courseraw = array();

    /** @var array */
    protected $category;

    public function get_paths() {
        return array(
            new convert_path(
                'course_header', '/MOODLE_BACKUP/COURSE/HEADER',
                array(
                    'newfields' => array(
                        'summaryformat'          => 1,
                        'legacyfiles'            => 1, // @todo is this correct?
                        'requested'              => 0, // @todo not really new, but maybe never backed up?
                        'restrictmodules'        => 0,
                        'enablecompletion'       => 0,
                        'completionstartonenrol' => 0,
                        'completionnotify'       => 0,
                    ),
                    'dropfields' => array(
                        'roles_overrides',
                        'roles_assignments',
                        'cost',
                        'currancy',
                        'defaultrole',
                        'enrol',
                        'enrolenddate',
                        'enrollable',
                        'enrolperiod',
                        'enrolstartdate',
                        'expirynotify',
                        'expirythreshold',
                        'guest',
                        'notifystudents',
                        'password',
                        'student',
                        'students',
                        'teacher',
                        'teachers',
                        'metacourse',
                    )
                )
            ),
            new convert_path('course_header_category', '/MOODLE_BACKUP/COURSE/HEADER/CATEGORY'),
        );
    }

    /**
     * Because there is the CATEGORY branch in the middle of the COURSE/HEADER
     * branch, this is dispatched twice. We use $this->coursecooked to merge
     * the result. Once the parser is fixed, it can be refactored.
     */
    public function process_course_header($data, $raw) {
       $this->course    = array_merge($this->course, $data);
       $this->courseraw = array_merge($this->courseraw, $raw);
    }

    public function process_course_header_category($data) {
        $this->category = $data;
    }

    public function on_course_header_end() {

        $contextid = convert_helper::get_contextid($this->course['id'], 'course', $this->converter->get_id());

        $this->open_xml_writer('course/course.xml');
        $this->xmlwriter->begin_tag('course', array(
            'id'        => $this->course['id'],
            'contextid' => $contextid,
        ));
        $this->write_xml($this->course);
        $this->xmlwriter->begin_tag('category', array('id' => $this->category['id']));
        $this->xmlwriter->full_tag('name', $this->category['name']);
        $this->xmlwriter->full_tag('description', null);
        $this->xmlwriter->end_tag('category');
        $this->xmlwriter->full_tag('tags', null);
        $this->xmlwriter->full_tag('allowed_modules', null);
        $this->xmlwriter->end_tag('course');
        $this->close_xml_writer();

    }
}
