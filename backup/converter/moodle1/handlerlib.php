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

        $handlers = array(
            new moodle1_root_handler($converter),
            new moodle1_info_handler($converter),
            new moodle1_course_header_handler($converter),
            new moodle1_course_outline_handler($converter),
        );

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
     * Writes the given XML tree data into the currently opened file
     *
     * @param string $element the name of the root element of the tree
     * @param array $data the associative array of data to write
     * @param array $attribs list of additional fields written as attributes instead of nested elements (all 'id' are there automatically)
     * @param string $parent used internally during the recursion, do not set yourself
     */
    public function write_xml($element, array $data, array $attribs = array(), $parent = '/') {

        $mypath    = $parent . $element;
        $myattribs = array();

        // detect properties that should be rendered as element's attributes instead of children
        foreach ($data as $name => $value) {
            if (!is_array($value)) {
                if ($name === 'id' or in_array($mypath . '/' . $name, $attribs)) {
                    $myattribs[$name] = $value;
                    unset($data[$name]);
                }
            }
        }

        // reorder the $data so that all sub-branches are at the end (needed by our parser)
        $leaves   = array();
        $branches = array();
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $branches[$name] = $value;
            } else {
                $leaves[$name] = $value;
            }
        }
        $data = array_merge($leaves, $branches);

        $this->xmlwriter->begin_tag($element, $myattribs);

        foreach ($data as $name => $value) {
            if (is_array($value)) {
                // recursively call self
                $this->write_xml($name, $value, $attribs, $mypath);
            } else {
                $this->xmlwriter->full_tag($name, $value);
            }
        }

        $this->xmlwriter->end_tag($element);
    }
}


/**
 * Process the root element of the backup file
 */
class moodle1_root_handler extends moodle1_xml_handler {

    public function get_paths() {
        return array(new convert_path('root_element', '/MOODLE_BACKUP'));
    }

    public function process_root_element($data) {
    }

    /**
     * This is executed at the very start of the moodle.xml parsing
     */
    public function on_root_element_start() {
    }

    /**
     * This is executed at the end of the moodle.xml parsing
     */
    public function on_root_element_end() {
        global $CFG;

        // restore the stashes prepared by other handlers for us
        $backupinfo         = $this->converter->get_stash('backup_info');
        $originalcourseinfo = $this->converter->get_stash('original_course_info');

        // start writing to the main XML file data
        $this->open_xml_writer('moodle_backup.xml');

        $this->xmlwriter->begin_tag('moodle_backup');
        $this->xmlwriter->begin_tag('information');

        // moodle_backup/information
        $this->xmlwriter->full_tag('name', $backupinfo['name']);
        $this->xmlwriter->full_tag('moodle_version', $backupinfo['moodle_version']);
        $this->xmlwriter->full_tag('moodle_release', $backupinfo['moodle_release']);
        $this->xmlwriter->full_tag('backup_version', $CFG->backup_version); // {@see restore_prechecks_helper::execute_prechecks}
        $this->xmlwriter->full_tag('backup_release', $CFG->backup_release);
        $this->xmlwriter->full_tag('backup_date', $backupinfo['date']);
        // see the commit c0543b - all backups created in 1.9 and later declare the
        // information or it is considered as false
        if (isset($backupinfo['mnet_remoteusers'])) {
            $this->xmlwriter->full_tag('mnet_remoteusers', $backupinfo['mnet_remoteusers']);
        } else {
            $this->xmlwriter->full_tag('mnet_remoteusers', false);
        }
        $this->xmlwriter->full_tag('original_wwwroot', $backupinfo['original_wwwroot']);
        // {@see backup_general_helper::backup_is_samesite()}
        if (isset($backupinfo['original_site_identifier_hash'])) {
            $this->xmlwriter->full_tag('original_site_identifier_hash', $backupinfo['original_site_identifier_hash']);
        }
        $this->xmlwriter->full_tag('original_course_id', $originalcourseinfo['original_course_id']);
        $this->xmlwriter->full_tag('original_course_fullname', $originalcourseinfo['original_course_fullname']);
        $this->xmlwriter->full_tag('original_course_shortname', $originalcourseinfo['original_course_shortname']);
        $this->xmlwriter->full_tag('original_course_startdate', $originalcourseinfo['original_course_startdate']);
        $this->xmlwriter->full_tag('original_course_contextid', $originalcourseinfo['original_course_contextid']);
        $this->xmlwriter->full_tag('original_system_contextid', $this->converter->get_contextid(CONTEXT_SYSTEM, 0));

        // moodle_backup/information/details
        $this->xmlwriter->begin_tag('details');
        $this->write_xml('detail', array(
            'backup_id'     => $this->converter->get_id(),
            'type'          => backup::TYPE_1COURSE,
            'format'        => backup::FORMAT_MOODLE,
            'interactive'   => backup::INTERACTIVE_YES,
            'mode'          => backup::MODE_CONVERTED,
            'execution'     => backup::EXECUTION_INMEDIATE,
            'executiontime' => 0,
        ), array('/detail/backup_id'));
        $this->xmlwriter->end_tag('details');

        // moodle_backup/information/contents
        $this->xmlwriter->begin_tag('contents');

        // moodle_backup/information/contents/activities
        $this->xmlwriter->begin_tag('activities');
        $activitysettings = array();
        foreach ($this->converter->get_stash('modnameslist') as $modname) {
            $modinfo = $this->converter->get_stash('modinfo_'.$modname);
            foreach ($modinfo['instances'] as $modinstanceid => $modinstance) {
                $cminfo = $this->converter->get_stash('cminfo_'.$modname, $modinstanceid);
                $activitysettings[] = array(
                    'level'     => 'activity',
                    'activity'  => $cminfo['modulename'].'_'.$cminfo['id'],
                    'name'      => $cminfo['modulename'].'_'.$cminfo['id'].'_included',
                    'value'     => (($modinfo['included'] === 'true' and $modinstance['included'] === 'true') ? 1 : 0));
                $activitysettings[] = array(
                    'level'     => 'activity',
                    'activity'  => $cminfo['modulename'].'_'.$cminfo['id'],
                    'name'      => $cminfo['modulename'].'_'.$cminfo['id'].'_userinfo',
                    'value'     => (($modinfo['userinfo'] === 'true' and $modinstance['userinfo'] === 'true') ? 1 : 0));
                $this->write_xml('activity', array(
                    'moduleid'      => $cminfo['id'],
                    'sectionid'     => $cminfo['sectionid'],
                    'modulename'    => $cminfo['modulename'],
                    'title'         => $modinstance['name'],
                    'directory'     => 'activities/'.$cminfo['modulename'].'_'.$cminfo['id']));

            }
        }
        $this->xmlwriter->end_tag('activities');

        // moodle_backup/information/contents/sections
        $this->xmlwriter->begin_tag('sections');
        $sectionsettings = array();
        foreach ($this->converter->get_stash('sectionidslist') as $sectionid) {
            $sectioninfo = $this->converter->get_stash('sectioninfo', $sectionid);
            $sectionsettings[] = array(
                'level'     => 'section',
                'section'   => 'section_'.$sectionid,
                'name'      => 'section_'.$sectionid.'_included',
                'value'     => 1);
            $sectionsettings[] = array(
                'level'     => 'section',
                'section'   => 'section_'.$sectionid,
                'name'      => 'section_'.$sectionid.'_userinfo',
                'value'     => 0); // @todo how to detect this from moodle.xml?
            $this->write_xml('section', array(
                'sectionid' => $sectionid,
                'title'     => $sectioninfo['number'], // because the title is not available
                'directory' => 'sections/section_'.$sectionid));
        }
        $this->xmlwriter->end_tag('sections');

        // moodle_backup/information/contents/course
        $this->write_xml('course', array(
            'courseid'  => $originalcourseinfo['original_course_id'],
            'title'     => $originalcourseinfo['original_course_shortname'],
            'directory' => 'course'));
        unset($originalcourseinfo);

        $this->xmlwriter->end_tag('contents');

        // moodle_backup/information/settings
        $this->xmlwriter->begin_tag('settings');

        // fake backup root seetings
        $rootsettings = array(
            'filename'         => $backupinfo['name'],
            'users'            => 0, // @todo how to detect this from moodle.xml?
            'anonymize'        => 0,
            'role_assignments' => 0,
            'user_files'       => 0,
            'activities'       => 1,
            'blocks'           => 1,
            'filters'          => 0,
            'comments'         => 0,
            'userscompletion'  => 0,
            'logs'             => 0,
            'grade_histories'  => 0,
        );
        unset($backupinfo);
        foreach ($rootsettings as $name => $value) {
            $this->write_xml('setting', array(
                'level' => 'root',
                'name'  => $name,
                'value' => $value));
        }
        unset($rootsettings);

        // activity settings populated above
        foreach ($activitysettings as $activitysetting) {
            $this->write_xml('setting', $activitysetting);
        }
        unset($activitysettings);

        // section settings populated above
        foreach ($sectionsettings as $sectionsetting) {
            $this->write_xml('setting', $sectionsetting);
        }
        unset($sectionsettings);

        $this->xmlwriter->end_tag('settings');

        $this->xmlwriter->end_tag('information');
        $this->xmlwriter->end_tag('moodle_backup');

        $this->close_xml_writer();
    }
}


/**
 * Handles the conversion of /MOODLE_BACKUP/INFO paths
 *
 * We do not produce any XML file here, just storing the data in the temp
 * table so thay can be used by a later handler.
 */
class moodle1_info_handler extends moodle1_handler {

    /** @var array list of mod names included in info_details */
    protected $modnames = array();

    /** @var array the in-memory cache of the currently parsed info_details_mod element */
    protected $currentmod;

    public function get_paths() {
        return array(
            new convert_path('info', '/MOODLE_BACKUP/INFO'),
            new convert_path('info_details', '/MOODLE_BACKUP/INFO/DETAILS'),
            new convert_path('info_details_mod', '/MOODLE_BACKUP/INFO/DETAILS/MOD'),
            new convert_path('info_details_mod_instance', '/MOODLE_BACKUP/INFO/DETAILS/MOD/INSTANCES/INSTANCE'),
        );
    }

    /**
     * Stashes the backup info for later processing by {@link moodle1_root_handler}
     */
    public function process_info($data) {
        $this->converter->set_stash('backup_info', $data);
    }

    public function process_info_details() {
    }

    /**
     * Initializes the in-memory cache for the current mod
     */
    public function process_info_details_mod($data) {
        $this->currentmod = $data;
    }

    /**
     * Appends the current instance data to the temporary in-memory cache
     */
    public function process_info_details_mod_instance($data) {
        $this->currentmod['instances'][$data['id']] = $data;
    }

    /**
     * Stashes the backup info for later processing by {@link moodle1_root_handler}
     */
    public function on_info_details_mod_end($data) {
        $this->converter->set_stash('modinfo_'.$this->currentmod['name'], $this->currentmod);
        $this->modnames[] = $this->currentmod['name'];
        $this->currentmod = array();
    }

    /**
     * Stashes the list of activity module types for later processing by {@link moodle1_root_handler}
     */
    public function on_info_details_end() {
        $this->converter->set_stash('modnameslist', $this->modnames);
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
                        'tags'                   => array(),
                        'allowed_modules'        => array(),
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
            new convert_path(
                'course_header_category', '/MOODLE_BACKUP/COURSE/HEADER/CATEGORY',
                array(
                    'newfields' => array(
                        'description' => null,
                    )
                )
            ),
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

        $contextid = $this->converter->get_contextid(CONTEXT_COURSE, $this->course['id']);

        // stash the information needed by other handlers
        $info = array(
            'original_course_id'        => $this->course['id'],
            'original_course_fullname'  => $this->course['fullname'],
            'original_course_shortname' => $this->course['shortname'],
            'original_course_startdate' => $this->course['startdate'],
            'original_course_contextid' => $contextid
        );
        $this->converter->set_stash('original_course_info', $info);

        $this->course['contextid'] = $contextid;
        $this->course['category'] = $this->category;

        $this->open_xml_writer('course/course.xml');
        $this->write_xml('course', $this->course, array('/course/contextid'));
        $this->close_xml_writer();
    }
}


/**
 * Handles the conversion of course sections and course modules
 */
class moodle1_course_outline_handler extends moodle1_xml_handler {

    /** @var array list of section ids */
    protected $sectionids = array();

    /** @var array current section data */
    protected $currentsection;

    /**
     * This handler is interested in course sections and course modules within them
     */
    public function get_paths() {
        return array(
            new convert_path('course_sections', '/MOODLE_BACKUP/COURSE/SECTIONS'),
            new convert_path(
                'course_section', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION',
                array(
                    'newfields' => array(
                        'name'          => null,
                        'summaryformat' => 1,
                        'sequence'      => null,
                    ),
                )
            ),
            new convert_path(
                'course_module', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD',
                array(
                    'newfields' => array(
                        'completion'                => 0,
                        'completiongradeitemnumber' => null,
                        'completionview'            => 0,
                        'completionexpected'        => 0,
                        'availablefrom'             => 0,
                        'availableuntil'            => 0,
                        'showavailability'          => 0,
                        'availability_info'         => array(),
                        'visibleold'                => 1,
                    ),
                    'dropfields' => array(
                        'instance',
                        'roles_overrides',
                        'roles_assignments',
                    ),
                    'renamefields' => array(
                        'type' => 'modulename',
                    ),
                )
            )
        );
    }

    public function process_course_sections() {
    }

    public function process_course_section($data) {
        $this->sectionids[]   = $data['id'];
        $this->currentsection = $data;
    }

    /**
     * Populates the section sequence field (order of course modules) and stashes the
     * course module info so that is can be dumped to activities/xxxx_x/module.xml later
     */
    public function process_course_module($data, $raw) {
        global $CFG;

        // add the course module id into the section's sequence
        if (is_null($this->currentsection['sequence'])) {
            $this->currentsection['sequence'] = $data['id'];
        } else {
            $this->currentsection['sequence'] .= ',' . $data['id'];
        }

        // add the sectionid and sectionnumber
        $data['sectionid']      = $this->currentsection['id'];
        $data['sectionnumber']  = $this->currentsection['number'];

        // generate the module version - this is a bit tricky as this information
        // is not present in 1.9 backups. we will use the currently installed version
        // whenever we can but that might not be accurate for some modules.
        // also there might be problem with modules that are not present at the target
        // host...
        $versionfile = $CFG->dirroot.'/mod/'.$data['modulename'].'/version.php';
        if (file_exists($versionfile)) {
            include($versionfile);
            $data['version'] = $module->version;
        } else {
            $data['version'] = null;
        }

        // stash the course module info in stashes like 'cminfo_forum' with
        // itemid set to the instance id. this is needed so that module handlers
        // can later obtain information about the course module.
        $this->converter->set_stash('cminfo_'.$data['modulename'], $data, $raw['INSTANCE']);

        // write the module.xml file
        $this->open_xml_writer('activities/'.$data['modulename'].'_'.$data['id'].'/module.xml');
        $this->write_xml('module', $data, array('/module/version'));
        $this->close_xml_writer();
    }

    /**
     * Writes sections/section_xxx/section.xml file and stashes it, too
     */
    public function on_course_section_end() {

        $this->converter->set_stash('sectioninfo', $this->currentsection, $this->currentsection['id']);
        $this->open_xml_writer('sections/section_' . $this->currentsection['id'] . '/section.xml');
        $this->write_xml('section', $this->currentsection);
        $this->close_xml_writer();
        unset($this->currentsection);
    }

    /**
     * Stashes the list of section ids
     */
    public function on_course_sections_end() {
        $this->converter->set_stash('sectionidslist', $this->sectionids);
        unset($this->sectionids);
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

    /**
     * Returns course module id for the given instance id
     *
     * The mapping from instance id to course module id has been stashed by
     * {@link moodle1_course_outline_handler::process_course_module()}
     *
     * @param int $instance the module instance id
     * @return int
     */
    protected function get_moduleid($instance) {

        $stashed = $this->converter->get_stash('cminfo_'.$this->pluginname, $instance);
        return $stashed['id'];
    }
}


/**
 * Base class for activity module handlers
 */
abstract class moodle1_block_handler extends moodle1_plugin_handler {

}
