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
            new moodle1_roles_definition_handler($converter),
            new moodle1_question_bank_handler($converter),
            new moodle1_scales_handler($converter),
            new moodle1_outcomes_handler($converter),
            new moodle1_gradebook_handler($converter),
        );

        $handlers = array_merge($handlers, self::get_plugin_handlers('mod', $converter));
        $handlers = array_merge($handlers, self::get_plugin_handlers('block', $converter));

        // make sure that all handlers have expected class
        foreach ($handlers as $handler) {
            if (!$handler instanceof moodle1_handler) {
                throw new moodle1_convert_exception('wrong_handler_class', get_class($handler));
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
     * @throws moodle1_convert_exception
     * @return array of {@link moodle1_handler} instances
     */
    protected static function get_plugin_handlers($type, moodle1_converter $converter) {
        global $CFG;

        $handlers = array();
        $plugins = core_component::get_plugin_list($type);
        foreach ($plugins as $name => $dir) {
            $handlerfile  = $dir . '/backup/moodle1/lib.php';
            $handlerclass = "moodle1_{$type}_{$name}_handler";
            if (file_exists($handlerfile)) {
                require_once($handlerfile);
            } elseif ($type == 'block') {
                $handlerclass = "moodle1_block_generic_handler";
            } else {
                continue;
            }

            if (!class_exists($handlerclass)) {
                throw new moodle1_convert_exception('missing_handler_class', $handlerclass);
            }
            $handlers[] = new $handlerclass($converter, $type, $name);
        }
        return $handlers;
    }
}


/**
 * Base backup conversion handler
 */
abstract class moodle1_handler implements loggable {

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

    /**
     * Log a message using the converter's logging mechanism
     *
     * @param string $message message text
     * @param int $level message level {@example backup::LOG_WARNING}
     * @param null|mixed $a additional information
     * @param null|int $depth the message depth
     * @param bool $display whether the message should be sent to the output, too
     */
    public function log($message, $level, $a = null, $depth = null, $display = false) {
        $this->converter->log($message, $level, $a, $depth, $display);
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
    protected function open_xml_writer($filename) {

        if (!is_null($this->xmlfilename) and $filename !== $this->xmlfilename) {
            throw new moodle1_convert_exception('xml_writer_already_opened_for_other_file', $this->xmlfilename);
        }

        if (!$this->xmlwriter instanceof xml_writer) {
            $this->xmlfilename = $filename;
            $fullpath  = $this->converter->get_workdir_path() . '/' . $this->xmlfilename;
            $directory = pathinfo($fullpath, PATHINFO_DIRNAME);

            if (!check_dir_exists($directory)) {
                throw new moodle1_convert_exception('unable_create_target_directory', $directory);
            }
            $this->xmlwriter = new xml_writer(new file_xml_output($fullpath), new moodle1_xml_transformer());
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
    protected function close_xml_writer() {
        if ($this->xmlwriter instanceof xml_writer) {
            $this->xmlwriter->stop();
        }
        unset($this->xmlwriter);
        $this->xmlwriter = null;
        $this->xmlfilename = null;
    }

    /**
     * Checks if the XML writer has been opened by {@link self::open_xml_writer()}
     *
     * @return bool
     */
    protected function has_xml_writer() {

        if ($this->xmlwriter instanceof xml_writer) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Writes the given XML tree data into the currently opened file
     *
     * @param string $element the name of the root element of the tree
     * @param array $data the associative array of data to write
     * @param array $attribs list of additional fields written as attributes instead of nested elements
     * @param string $parent used internally during the recursion, do not set yourself
     */
    protected function write_xml($element, array $data, array $attribs = array(), $parent = '/') {

        if (!$this->has_xml_writer()) {
            throw new moodle1_convert_exception('write_xml_without_writer');
        }

        $mypath    = $parent . $element;
        $myattribs = array();

        // detect properties that should be rendered as element's attributes instead of children
        foreach ($data as $name => $value) {
            if (!is_array($value)) {
                if (in_array($mypath . '/' . $name, $attribs)) {
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
                $this->write_xml($name, $value, $attribs, $mypath.'/');
            } else {
                $this->xmlwriter->full_tag($name, $value);
            }
        }

        $this->xmlwriter->end_tag($element);
    }

    /**
     * Makes sure that a new XML file exists, or creates it itself
     *
     * This is here so we can check that all XML files that the restore process relies on have
     * been created by an executed handler. If the file is not found, this method can create it
     * using the given $rootelement as an empty root container in the file.
     *
     * @param string $filename relative file name like 'course/course.xml'
     * @param string|bool $rootelement root element to use, false to not create the file
     * @param array $content content of the root element
     * @return bool true is the file existed, false if it did not
     */
    protected function make_sure_xml_exists($filename, $rootelement = false, $content = array()) {

        $existed = file_exists($this->converter->get_workdir_path().'/'.$filename);

        if ($existed) {
            return true;
        }

        if ($rootelement !== false) {
            $this->open_xml_writer($filename);
            $this->write_xml($rootelement, $content);
            $this->close_xml_writer();
        }

        return false;
    }
}


/**
 * Process the root element of the backup file
 */
class moodle1_root_handler extends moodle1_xml_handler {

    public function get_paths() {
        return array(new convert_path('root_element', '/MOODLE_BACKUP'));
    }

    /**
     * Converts course_files and site_files
     */
    public function on_root_element_start() {

        // convert course files
        $fileshandler = new moodle1_files_handler($this->converter);
        $fileshandler->process();
    }

    /**
     * This is executed at the end of the moodle.xml parsing
     */
    public function on_root_element_end() {
        global $CFG;

        // restore the stashes prepared by other handlers for us
        $backupinfo         = $this->converter->get_stash('backup_info');
        $originalcourseinfo = $this->converter->get_stash('original_course_info');

        ////////////////////////////////////////////////////////////////////////
        // write moodle_backup.xml
        ////////////////////////////////////////////////////////////////////////
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
        } else {
            $this->xmlwriter->full_tag('original_site_identifier_hash', null);
        }
        $this->xmlwriter->full_tag('original_course_id', $originalcourseinfo['original_course_id']);
        $this->xmlwriter->full_tag('original_course_fullname', $originalcourseinfo['original_course_fullname']);
        $this->xmlwriter->full_tag('original_course_shortname', $originalcourseinfo['original_course_shortname']);
        $this->xmlwriter->full_tag('original_course_startdate', $originalcourseinfo['original_course_startdate']);
        $this->xmlwriter->full_tag('original_system_contextid', $this->converter->get_contextid(CONTEXT_SYSTEM));
        // note that even though we have original_course_contextid available, we regenerate the
        // original course contextid using our helper method to be sure that the data are consistent
        // within the MBZ file
        $this->xmlwriter->full_tag('original_course_contextid', $this->converter->get_contextid(CONTEXT_COURSE));

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
        foreach ($this->converter->get_stash('coursecontents') as $activity) {
            $modinfo = $this->converter->get_stash('modinfo_'.$activity['modulename']);
            $modinstance = $modinfo['instances'][$activity['instanceid']];
            $this->write_xml('activity', array(
                'moduleid'      => $activity['cmid'],
                'sectionid'     => $activity['sectionid'],
                'modulename'    => $activity['modulename'],
                'title'         => $modinstance['name'],
                'directory'     => 'activities/'.$activity['modulename'].'_'.$activity['cmid']
            ));
            $activitysettings[] = array(
                'level'     => 'activity',
                'activity'  => $activity['modulename'].'_'.$activity['cmid'],
                'name'      => $activity['modulename'].'_'.$activity['cmid'].'_included',
                'value'     => (($modinfo['included'] === 'true' and $modinstance['included'] === 'true') ? 1 : 0));
            $activitysettings[] = array(
                'level'     => 'activity',
                'activity'  => $activity['modulename'].'_'.$activity['cmid'],
                'name'      => $activity['modulename'].'_'.$activity['cmid'].'_userinfo',
                //'value'     => (($modinfo['userinfo'] === 'true' and $modinstance['userinfo'] === 'true') ? 1 : 0));
                'value'     => 0); // todo hardcoded non-userinfo for now
        }
        $this->xmlwriter->end_tag('activities');

        // moodle_backup/information/contents/sections
        $this->xmlwriter->begin_tag('sections');
        $sectionsettings = array();
        foreach ($this->converter->get_stash_itemids('sectioninfo') as $sectionid) {
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

        ////////////////////////////////////////////////////////////////////////
        // write files.xml
        ////////////////////////////////////////////////////////////////////////
        $this->open_xml_writer('files.xml');
        $this->xmlwriter->begin_tag('files');
        foreach ($this->converter->get_stash_itemids('files') as $fileid) {
            $this->write_xml('file', $this->converter->get_stash('files', $fileid), array('/file/id'));
        }
        $this->xmlwriter->end_tag('files');
        $this->close_xml_writer('files.xml');

        ////////////////////////////////////////////////////////////////////////
        // write scales.xml
        ////////////////////////////////////////////////////////////////////////
        $this->open_xml_writer('scales.xml');
        $this->xmlwriter->begin_tag('scales_definition');
        foreach ($this->converter->get_stash_itemids('scales') as $scaleid) {
            $this->write_xml('scale', $this->converter->get_stash('scales', $scaleid), array('/scale/id'));
        }
        $this->xmlwriter->end_tag('scales_definition');
        $this->close_xml_writer('scales.xml');

        ////////////////////////////////////////////////////////////////////////
        // write course/inforef.xml
        ////////////////////////////////////////////////////////////////////////
        $this->open_xml_writer('course/inforef.xml');
        $this->xmlwriter->begin_tag('inforef');

        $this->xmlwriter->begin_tag('fileref');
        // legacy course files
        $fileids = $this->converter->get_stash('course_files_ids');
        if (is_array($fileids)) {
            foreach ($fileids as $fileid) {
                $this->write_xml('file', array('id' => $fileid));
            }
        }
        // todo site files
        // course summary files
        $fileids = $this->converter->get_stash('course_summary_files_ids');
        if (is_array($fileids)) {
            foreach ($fileids as $fileid) {
                $this->write_xml('file', array('id' => $fileid));
            }
        }
        $this->xmlwriter->end_tag('fileref');

        $this->xmlwriter->begin_tag('question_categoryref');
        foreach ($this->converter->get_stash_itemids('question_categories') as $questioncategoryid) {
            $this->write_xml('question_category', array('id' => $questioncategoryid));
        }
        $this->xmlwriter->end_tag('question_categoryref');

        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();

        // make sure that the files required by the restore process have been generated.
        // missing file may happen if the watched tag is not present in moodle.xml (for example
        // QUESTION_CATEGORIES is optional in moodle.xml but questions.xml must exist in
        // moodle2 format) or the handler has not been implemented yet.
        // apparently this must be called after the handler had a chance to create the file.
        $this->make_sure_xml_exists('questions.xml', 'question_categories');
        $this->make_sure_xml_exists('groups.xml', 'groups');
        $this->make_sure_xml_exists('outcomes.xml', 'outcomes_definition');
        $this->make_sure_xml_exists('users.xml', 'users');
        $this->make_sure_xml_exists('course/roles.xml', 'roles',
            array('role_assignments' => array(), 'role_overrides' => array()));
        $this->make_sure_xml_exists('course/enrolments.xml', 'enrolments',
            array('enrols' => array()));
    }
}


/**
 * The class responsible for course and site files migration
 *
 * @todo migrate site_files
 */
class moodle1_files_handler extends moodle1_xml_handler {

    /**
     * Migrates course_files and site_files in the converter workdir
     */
    public function process() {
        $this->migrate_course_files();
        // todo $this->migrate_site_files();
    }

    /**
     * Migrates course_files in the converter workdir
     */
    protected function migrate_course_files() {
        $ids  = array();
        $fileman = $this->converter->get_file_manager($this->converter->get_contextid(CONTEXT_COURSE), 'course', 'legacy');
        $this->converter->set_stash('course_files_ids', array());
        if (file_exists($this->converter->get_tempdir_path().'/course_files')) {
            $ids = $fileman->migrate_directory('course_files');
            $this->converter->set_stash('course_files_ids', $ids);
        }
        $this->log('course files migrated', backup::LOG_INFO, count($ids));
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

    /**
     * Initializes the in-memory cache for the current mod
     */
    public function process_info_details_mod($data) {
        $this->currentmod = $data;
        $this->currentmod['instances'] = array();
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
        global $CFG;

        // keep only such modules that seem to have the support for moodle1 implemented
        $modname = $this->currentmod['name'];
        if (file_exists($CFG->dirroot.'/mod/'.$modname.'/backup/moodle1/lib.php')) {
            $this->converter->set_stash('modinfo_'.$modname, $this->currentmod);
            $this->modnames[] = $modname;
        } else {
            $this->log('unsupported activity module', backup::LOG_WARNING, $modname);
        }

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
                        'legacyfiles'            => 2,
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

        $contextid = $this->converter->get_contextid(CONTEXT_COURSE);

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

        // migrate files embedded into the course summary and stash their ids
        $fileman = $this->converter->get_file_manager($contextid, 'course', 'summary');
        $this->course['summary'] = moodle1_converter::migrate_referenced_files($this->course['summary'], $fileman);
        $this->converter->set_stash('course_summary_files_ids', $fileman->get_fileids());

        // write course.xml
        $this->open_xml_writer('course/course.xml');
        $this->write_xml('course', $this->course, array('/course/id', '/course/contextid'));
        $this->close_xml_writer();
    }
}


/**
 * Handles the conversion of course sections and course modules
 */
class moodle1_course_outline_handler extends moodle1_xml_handler {

    /** @var array ordered list of the course contents */
    protected $coursecontents = array();

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
                        'availability'              => null,
                        'visibleold'                => 1,
                        'showdescription'           => 0,
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
            ),
            new convert_path('course_modules', '/MOODLE_BACKUP/COURSE/MODULES'),
            // todo new convert_path('course_module_roles_overrides', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_OVERRIDES'),
            // todo new convert_path('course_module_roles_assignments', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_ASSIGNMENTS'),
        );
    }

    public function process_course_section($data) {
        $this->currentsection = $data;
    }

    /**
     * Populates the section sequence field (order of course modules) and stashes the
     * course module info so that is can be dumped to activities/xxxx_x/module.xml later
     */
    public function process_course_module($data, $raw) {
        global $CFG;

        // check that this type of module should be included in the mbz
        $modinfo = $this->converter->get_stash_itemids('modinfo_'.$data['modulename']);
        if (empty($modinfo)) {
            return;
        }

        // add the course module into the course contents list
        $this->coursecontents[$data['id']] = array(
            'cmid'       => $data['id'],
            'instanceid' => $raw['INSTANCE'],
            'sectionid'  => $this->currentsection['id'],
            'modulename' => $data['modulename'],
            'title'      => null
        );

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
            $plugin = new stdClass();
            $plugin->version = null;
            $module = $plugin;
            include($versionfile);
            // Have to hardcode - since quiz uses some hardcoded version numbers when restoring.
            // This is the lowest number used minus one.
            $data['version'] = 2011010099;
        } else {
            $data['version'] = null;
        }

        // stash the course module info in stashes like 'cminfo_forum' with
        // itemid set to the instance id. this is needed so that module handlers
        // can later obtain information about the course module and dump it into
        // the module.xml file
        $this->converter->set_stash('cminfo_'.$data['modulename'], $data, $raw['INSTANCE']);
    }

    /**
     * Writes sections/section_xxx/section.xml file and stashes it, too
     */
    public function on_course_section_end() {

        // migrate files embedded into the section summary field
        $contextid = $this->converter->get_contextid(CONTEXT_COURSE);
        $fileman = $this->converter->get_file_manager($contextid, 'course', 'section', $this->currentsection['id']);
        $this->currentsection['summary'] = moodle1_converter::migrate_referenced_files($this->currentsection['summary'], $fileman);

        // write section's inforef.xml with the file references
        $this->open_xml_writer('sections/section_' . $this->currentsection['id'] . '/inforef.xml');
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        $fileids = $fileman->get_fileids();
        if (is_array($fileids)) {
            foreach ($fileids as $fileid) {
                $this->write_xml('file', array('id' => $fileid));
            }
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();

        // stash the section info and write section.xml
        $this->converter->set_stash('sectioninfo', $this->currentsection, $this->currentsection['id']);
        $this->open_xml_writer('sections/section_' . $this->currentsection['id'] . '/section.xml');
        $this->write_xml('section', $this->currentsection);
        $this->close_xml_writer();
        unset($this->currentsection);
    }

    /**
     * Stashes the course contents
     */
    public function on_course_sections_end() {
        $this->converter->set_stash('coursecontents', $this->coursecontents);
    }

    /**
     * Writes the information collected by mod handlers
     */
    public function on_course_modules_end() {

        foreach ($this->converter->get_stash('modnameslist') as $modname) {
            $modinfo = $this->converter->get_stash('modinfo_'.$modname);
            foreach ($modinfo['instances'] as $modinstanceid => $modinstance) {
                $cminfo    = $this->converter->get_stash('cminfo_'.$modname, $modinstanceid);
                $directory = 'activities/'.$modname.'_'.$cminfo['id'];

                // write module.xml
                $this->open_xml_writer($directory.'/module.xml');
                $this->write_xml('module', $cminfo, array('/module/id', '/module/version'));
                $this->close_xml_writer();

                // write grades.xml
                $this->open_xml_writer($directory.'/grades.xml');
                $this->xmlwriter->begin_tag('activity_gradebook');
                $gradeitems = $this->converter->get_stash_or_default('gradebook_modgradeitem_'.$modname, $modinstanceid, array());
                if (!empty($gradeitems)) {
                    $this->xmlwriter->begin_tag('grade_items');
                    foreach ($gradeitems as $gradeitem) {
                        $this->write_xml('grade_item', $gradeitem, array('/grade_item/id'));
                    }
                    $this->xmlwriter->end_tag('grade_items');
                }
                $this->write_xml('grade_letters', array()); // no grade_letters in module context in Moodle 1.9
                $this->xmlwriter->end_tag('activity_gradebook');
                $this->close_xml_writer();

                // todo: write proper roles.xml, for now we just make sure the file is present
                $this->make_sure_xml_exists($directory.'/roles.xml', 'roles');
            }
        }
    }
}


/**
 * Handles the conversion of the defined roles
 */
class moodle1_roles_definition_handler extends moodle1_xml_handler {

    /**
     * Where the roles are defined in the source moodle.xml
     */
    public function get_paths() {
        return array(
            new convert_path('roles', '/MOODLE_BACKUP/ROLES'),
            new convert_path(
                'roles_role', '/MOODLE_BACKUP/ROLES/ROLE',
                array(
                    'newfields' => array(
                        'description'   => '',
                        'sortorder'     => 0,
                        'archetype'     => ''
                    )
                )
            )
        );
    }

    /**
     * If there are any roles defined in moodle.xml, convert them to roles.xml
     */
    public function process_roles_role($data) {

        if (!$this->has_xml_writer()) {
            $this->open_xml_writer('roles.xml');
            $this->xmlwriter->begin_tag('roles_definition');
        }
        if (!isset($data['nameincourse'])) {
            $data['nameincourse'] = null;
        }
        $this->write_xml('role', $data, array('role/id'));
    }

    /**
     * Finishes writing roles.xml
     */
    public function on_roles_end() {

        if (!$this->has_xml_writer()) {
            // no roles defined in moodle.xml so {link self::process_roles_role()}
            // was never executed
            $this->open_xml_writer('roles.xml');
            $this->write_xml('roles_definition', array());

        } else {
            // some roles were dumped into the file, let us close their wrapper now
            $this->xmlwriter->end_tag('roles_definition');
        }
        $this->close_xml_writer();
    }
}


/**
 * Handles the conversion of the question bank included in the moodle.xml file
 */
class moodle1_question_bank_handler extends moodle1_xml_handler {

    /** @var array the current question category being parsed */
    protected $currentcategory = null;

    /** @var array of the raw data for the current category */
    protected $currentcategoryraw = null;

    /** @var moodle1_file_manager instance used to convert question images */
    protected $fileman = null;

    /** @var bool are the currentcategory data already written (this is a work around MDL-27693) */
    private $currentcategorywritten = false;

    /** @var bool was the <questions> tag already written (work around MDL-27693) */
    private $questionswrapperwritten = false;

    /** @var array holds the instances of qtype specific conversion handlers */
    private $qtypehandlers = null;

    /**
     * Return the file manager instance used.
     *
     * @return moodle1_file_manager
     */
    public function get_file_manager() {
        return $this->fileman;
    }

    /**
     * Returns the information about the question category context being currently parsed
     *
     * @return array with keys contextid, contextlevel and contextinstanceid
     */
    public function get_current_category_context() {
        return $this->currentcategory;
    }

    /**
     * Registers path that are not qtype-specific
     */
    public function get_paths() {

        $paths = array(
            new convert_path('question_categories', '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES'),
            new convert_path(
                'question_category', '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES/QUESTION_CATEGORY',
                array(
                    'newfields' => array(
                        'infoformat' => 0
                    )
                )),
            new convert_path('question_category_context', '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES/QUESTION_CATEGORY/CONTEXT'),
            new convert_path('questions', '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES/QUESTION_CATEGORY/QUESTIONS'),
            // the question element must be grouped so we can re-dispatch it to the qtype handler as a whole
            new convert_path('question', '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES/QUESTION_CATEGORY/QUESTIONS/QUESTION', array(), true),
        );

        // annotate all question subpaths required by the qtypes subplugins
        $subpaths = array();
        foreach ($this->get_qtype_handler('*') as $qtypehandler) {
            foreach ($qtypehandler->get_question_subpaths() as $subpath) {
                $subpaths[$subpath] = true;
            }
        }
        foreach (array_keys($subpaths) as $subpath) {
            $name = 'subquestion_'.strtolower(str_replace('/', '_', $subpath));
            $path = '/MOODLE_BACKUP/COURSE/QUESTION_CATEGORIES/QUESTION_CATEGORY/QUESTIONS/QUESTION/'.$subpath;
            $paths[] = new convert_path($name, $path);
        }

        return $paths;
    }

    /**
     * Starts writing questions.xml and prepares the file manager instance
     */
    public function on_question_categories_start() {
        $this->open_xml_writer('questions.xml');
        $this->xmlwriter->begin_tag('question_categories');
        if (is_null($this->fileman)) {
            $this->fileman = $this->converter->get_file_manager();
        }
    }

    /**
     * Initializes the current category cache
     */
    public function on_question_category_start() {
        $this->currentcategory         = array();
        $this->currentcategoryraw      = array();
        $this->currentcategorywritten  = false;
        $this->questionswrapperwritten = false;
    }

    /**
     * Populates the current question category data
     *
     * Bacuse of the known subpath-in-the-middle problem (CONTEXT in this case), this is actually
     * called twice for both halves of the data. We merge them here into the currentcategory array.
     */
    public function process_question_category($data, $raw) {
        $this->currentcategory    = array_merge($this->currentcategory, $data);
        $this->currentcategoryraw = array_merge($this->currentcategoryraw, $raw);
    }

    /**
     * Inject the context related information into the current category
     */
    public function process_question_category_context($data) {

        switch ($data['level']) {
        case 'module':
            $this->currentcategory['contextid'] = $this->converter->get_contextid(CONTEXT_MODULE, $data['instance']);
            $this->currentcategory['contextlevel'] = CONTEXT_MODULE;
            $this->currentcategory['contextinstanceid'] = $data['instance'];
            break;
        case 'course':
            $originalcourseinfo = $this->converter->get_stash('original_course_info');
            $originalcourseid   = $originalcourseinfo['original_course_id'];
            $this->currentcategory['contextid'] = $this->converter->get_contextid(CONTEXT_COURSE);
            $this->currentcategory['contextlevel'] = CONTEXT_COURSE;
            $this->currentcategory['contextinstanceid'] = $originalcourseid;
            break;
        case 'coursecategory':
            // this is a bit hacky. the source moodle.xml defines COURSECATEGORYLEVEL as a distance
            // of the course category (1 = parent category, 2 = grand-parent category etc). We pretend
            // that this level*10 is the id of that category and create an artifical contextid for it
            $this->currentcategory['contextid'] = $this->converter->get_contextid(CONTEXT_COURSECAT, $data['coursecategorylevel'] * 10);
            $this->currentcategory['contextlevel'] = CONTEXT_COURSECAT;
            $this->currentcategory['contextinstanceid'] = $data['coursecategorylevel'] * 10;
            break;
        case 'system':
            $this->currentcategory['contextid'] = $this->converter->get_contextid(CONTEXT_SYSTEM);
            $this->currentcategory['contextlevel'] = CONTEXT_SYSTEM;
            $this->currentcategory['contextinstanceid'] = 0;
            break;
        }
    }

    /**
     * Writes the common <question> data and re-dispateches the whole grouped
     * <QUESTION> data to the qtype for appending its qtype specific data processing
     *
     * @param array $data
     * @param array $raw
     * @return array
     */
    public function process_question(array $data, array $raw) {
        global $CFG;

        // firstly make sure that the category data and the <questions> wrapper are written
        // note that because of MDL-27693 we can't use {@link self::process_question_category()}
        // and {@link self::on_questions_start()} to do so

        if (empty($this->currentcategorywritten)) {
            $this->xmlwriter->begin_tag('question_category', array('id' => $this->currentcategory['id']));
            foreach ($this->currentcategory as $name => $value) {
                if ($name === 'id') {
                    continue;
                }
                $this->xmlwriter->full_tag($name, $value);
            }
            $this->currentcategorywritten = true;
        }

        if (empty($this->questionswrapperwritten)) {
            $this->xmlwriter->begin_tag('questions');
            $this->questionswrapperwritten = true;
        }

        $qtype = $data['qtype'];

        // replay the upgrade step 2008050700 {@see question_fix_random_question_parents()}
        if ($qtype == 'random' and $data['parent'] <> $data['id']) {
            $data['parent'] = $data['id'];
        }

        // replay the upgrade step 2010080900 and part of 2010080901
        $data['generalfeedbackformat'] = $data['questiontextformat'];
        $data['oldquestiontextformat'] = $data['questiontextformat'];

        if ($CFG->texteditors !== 'textarea') {
            $data['questiontext'] = text_to_html($data['questiontext'], false, false, true);
            $data['questiontextformat'] = FORMAT_HTML;
            $data['generalfeedback'] = text_to_html($data['generalfeedback'], false, false, true);
            $data['generalfeedbackformat'] = FORMAT_HTML;
        }

        // Migrate files in questiontext.
        $this->fileman->contextid = $this->currentcategory['contextid'];
        $this->fileman->component = 'question';
        $this->fileman->filearea  = 'questiontext';
        $this->fileman->itemid    = $data['id'];
        $data['questiontext'] = moodle1_converter::migrate_referenced_files($data['questiontext'], $this->fileman);

        // Migrate files in generalfeedback.
        $this->fileman->filearea  = 'generalfeedback';
        $data['generalfeedback'] = moodle1_converter::migrate_referenced_files($data['generalfeedback'], $this->fileman);

        // replay the upgrade step 2010080901 - updating question image
        if (!empty($data['image'])) {
            if (core_text::substr(core_text::strtolower($data['image']), 0, 7) == 'http://') {
                // it is a link, appending to existing question text
                $data['questiontext'] .= ' <img src="' . $data['image'] . '" />';

            } else {
                // it is a file in course_files
                $filename = basename($data['image']);
                $filepath = dirname($data['image']);
                if (empty($filepath) or $filepath == '.' or $filepath == '/') {
                    $filepath = '/';
                } else {
                    // append /
                    $filepath = '/'.trim($filepath, './@#$ ').'/';
                }

                if (file_exists($this->converter->get_tempdir_path().'/course_files'.$filepath.$filename)) {
                    $this->fileman->contextid = $this->currentcategory['contextid'];
                    $this->fileman->component = 'question';
                    $this->fileman->filearea  = 'questiontext';
                    $this->fileman->itemid    = $data['id'];
                    $this->fileman->migrate_file('course_files'.$filepath.$filename, '/', $filename);
                    // note this is slightly different from the upgrade code as we put the file into the
                    // root folder here. this makes our life easier as we do not need to create all the
                    // directories within the specified filearea/itemid
                    $data['questiontext'] .= ' <img src="@@PLUGINFILE@@/' . $filename . '" />';

                } else {
                    $this->log('question file not found', backup::LOG_WARNING, array($data['id'], $filepath.$filename));
                }
            }
        }
        unset($data['image']);

        // replay the upgrade step 2011060301 - Rename field defaultgrade on table question to defaultmark
        $data['defaultmark'] = $data['defaultgrade'];

        // write the common question data
        $this->xmlwriter->begin_tag('question', array('id' => $data['id']));
        foreach (array(
            'parent', 'name', 'questiontext', 'questiontextformat',
            'generalfeedback', 'generalfeedbackformat', 'defaultmark',
            'penalty', 'qtype', 'length', 'stamp', 'version', 'hidden',
            'timecreated', 'timemodified', 'createdby', 'modifiedby'
        ) as $fieldname) {
            if (!array_key_exists($fieldname, $data)) {
                throw new moodle1_convert_exception('missing_common_question_field', $fieldname);
            }
            $this->xmlwriter->full_tag($fieldname, $data[$fieldname]);
        }
        // unless we know that the given qtype does not append any own structures,
        // give the handler a chance to do so now
        if (!in_array($qtype, array('description', 'random'))) {
            $handler = $this->get_qtype_handler($qtype);
            if ($handler === false) {
                $this->log('question type converter not found', backup::LOG_ERROR, $qtype);

            } else {
                $this->xmlwriter->begin_tag('plugin_qtype_'.$qtype.'_question');
                $handler->use_xml_writer($this->xmlwriter);
                $handler->process_question($data, $raw);
                $this->xmlwriter->end_tag('plugin_qtype_'.$qtype.'_question');
            }
        }

        $this->xmlwriter->end_tag('question');
    }

    /**
     * Closes the questions wrapper
     */
    public function on_questions_end() {
        if ($this->questionswrapperwritten) {
            $this->xmlwriter->end_tag('questions');
        }
    }

    /**
     * Closes the question_category and annotates the category id
     * so that it can be dumped into course/inforef.xml
     */
    public function on_question_category_end() {
        // make sure that the category data were written by {@link self::process_question()}
        // if not, write it now. this may happen when the current category does not contain any
        // questions so the subpaths is missing completely
        if (empty($this->currentcategorywritten)) {
            $this->write_xml('question_category', $this->currentcategory, array('/question_category/id'));
        } else {
            $this->xmlwriter->end_tag('question_category');
        }
        $this->converter->set_stash('question_categories', $this->currentcategory, $this->currentcategory['id']);
    }

    /**
     * Stops writing questions.xml
     */
    public function on_question_categories_end() {
        $this->xmlwriter->end_tag('question_categories');
        $this->close_xml_writer();
    }

    /**
     * Provides access to the qtype handlers
     *
     * Returns either list of all qtype handler instances (if passed '*') or a particular handler
     * for the given qtype or false if the qtype is not supported.
     *
     * @throws moodle1_convert_exception
     * @param string $qtype the name of the question type or '*' for returning all
     * @return array|moodle1_qtype_handler|bool
     */
    protected function get_qtype_handler($qtype) {

        if (is_null($this->qtypehandlers)) {
            // initialize the list of qtype handler instances
            $this->qtypehandlers = array();
            foreach (core_component::get_plugin_list('qtype') as $qtypename => $qtypelocation) {
                $filename = $qtypelocation.'/backup/moodle1/lib.php';
                if (file_exists($filename)) {
                    $classname = 'moodle1_qtype_'.$qtypename.'_handler';
                    require_once($filename);
                    if (!class_exists($classname)) {
                        throw new moodle1_convert_exception('missing_handler_class', $classname);
                    }
                    $this->log('registering handler', backup::LOG_DEBUG, $classname, 2);
                    $this->qtypehandlers[$qtypename] = new $classname($this, $qtypename);
                }
            }
        }

        if ($qtype === '*') {
            return $this->qtypehandlers;

        } else if (isset($this->qtypehandlers[$qtype])) {
            return $this->qtypehandlers[$qtype];

        } else {
            return false;
        }
    }
}


/**
 * Handles the conversion of the scales included in the moodle.xml file
 */
class moodle1_scales_handler extends moodle1_handler {

    /** @var moodle1_file_manager instance used to convert question images */
    protected $fileman = null;

    /**
     * Registers paths
     */
    public function get_paths() {
        return array(
            new convert_path('scales', '/MOODLE_BACKUP/COURSE/SCALES'),
            new convert_path(
                'scale', '/MOODLE_BACKUP/COURSE/SCALES/SCALE',
                array(
                    'renamefields' => array(
                        'scaletext' => 'scale',
                    ),
                    'addfields' => array(
                        'descriptionformat' => 0,
                    )
                )
            ),
        );
    }

    /**
     * Prepare the file manager for the files embedded in the scale description field
     */
    public function on_scales_start() {
        $syscontextid  = $this->converter->get_contextid(CONTEXT_SYSTEM);
        $this->fileman = $this->converter->get_file_manager($syscontextid, 'grade', 'scale');
    }

    /**
     * This is executed every time we have one <SCALE> data available
     *
     * @param array $data
     * @param array $raw
     * @return array
     */
    public function process_scale(array $data, array $raw) {
        global $CFG;

        // replay upgrade step 2009110400
        if ($CFG->texteditors !== 'textarea') {
            $data['description'] = text_to_html($data['description'], false, false, true);
            $data['descriptionformat'] = FORMAT_HTML;
        }

        // convert course files embedded into the scale description field
        $this->fileman->itemid = $data['id'];
        $data['description'] = moodle1_converter::migrate_referenced_files($data['description'], $this->fileman);

        // stash the scale
        $this->converter->set_stash('scales', $data, $data['id']);
    }
}


/**
 * Handles the conversion of the outcomes
 */
class moodle1_outcomes_handler extends moodle1_xml_handler {

    /** @var moodle1_file_manager instance used to convert images embedded into outcome descriptions */
    protected $fileman = null;

    /**
     * Registers paths
     */
    public function get_paths() {
        return array(
            new convert_path('gradebook_grade_outcomes', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_OUTCOMES'),
            new convert_path(
                'gradebook_grade_outcome', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_OUTCOMES/GRADE_OUTCOME',
                array(
                    'addfields' => array(
                        'descriptionformat' => FORMAT_MOODLE,
                    ),
                )
            ),
        );
    }

    /**
     * Prepares the file manager and starts writing outcomes.xml
     */
    public function on_gradebook_grade_outcomes_start() {

        $syscontextid  = $this->converter->get_contextid(CONTEXT_SYSTEM);
        $this->fileman = $this->converter->get_file_manager($syscontextid, 'grade', 'outcome');

        $this->open_xml_writer('outcomes.xml');
        $this->xmlwriter->begin_tag('outcomes_definition');
    }

    /**
     * Processes GRADE_OUTCOME tags progressively
     */
    public function process_gradebook_grade_outcome(array $data, array $raw) {
        global $CFG;

        // replay the upgrade step 2009110400
        if ($CFG->texteditors !== 'textarea') {
            $data['description']       = text_to_html($data['description'], false, false, true);
            $data['descriptionformat'] = FORMAT_HTML;
        }

        // convert course files embedded into the outcome description field
        $this->fileman->itemid = $data['id'];
        $data['description'] = moodle1_converter::migrate_referenced_files($data['description'], $this->fileman);

        // write the outcome data
        $this->write_xml('outcome', $data, array('/outcome/id'));

        return $data;
    }

    /**
     * Closes outcomes.xml
     */
    public function on_gradebook_grade_outcomes_end() {
        $this->xmlwriter->end_tag('outcomes_definition');
        $this->close_xml_writer();
    }
}


/**
 * Handles the conversion of the gradebook structures in the moodle.xml file
 */
class moodle1_gradebook_handler extends moodle1_xml_handler {

    /** @var array of (int)gradecategoryid => (int|null)parentcategoryid */
    protected $categoryparent = array();

    /**
     * Registers paths
     */
    public function get_paths() {
        return array(
            new convert_path('gradebook', '/MOODLE_BACKUP/COURSE/GRADEBOOK'),
            new convert_path('gradebook_grade_letter', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_LETTERS/GRADE_LETTER'),
            new convert_path(
                'gradebook_grade_category', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_CATEGORIES/GRADE_CATEGORY',
                array(
                    'addfields' => array(
                        'hidden' => 0,  // upgrade step 2010011200
                    ),
                )
            ),
            new convert_path('gradebook_grade_item', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_ITEMS/GRADE_ITEM'),
            new convert_path('gradebook_grade_item_grades', '/MOODLE_BACKUP/COURSE/GRADEBOOK/GRADE_ITEMS/GRADE_ITEM/GRADE_GRADES'),
        );
    }

    /**
     * Initializes the in-memory structures
     *
     * This should not be needed actually as the moodle.xml contains just one GRADEBOOK
     * element. But who knows - maybe someone will want to write a mass conversion
     * tool in the future (not me definitely ;-)
     */
    public function on_gradebook_start() {
        $this->categoryparent = array();
    }

    /**
     * Processes one GRADE_LETTER data
     *
     * In Moodle 1.9, all grade_letters are from course context only. Therefore
     * we put them here.
     */
    public function process_gradebook_grade_letter(array $data, array $raw) {
        $this->converter->set_stash('gradebook_gradeletter', $data, $data['id']);
    }

    /**
     * Processes one GRADE_CATEGORY data
     */
    public function process_gradebook_grade_category(array $data, array $raw) {
        $this->categoryparent[$data['id']] = $data['parent'];
        $this->converter->set_stash('gradebook_gradecategory', $data, $data['id']);
    }

    /**
     * Processes one GRADE_ITEM data
     */
    public function process_gradebook_grade_item(array $data, array $raw) {

        // here we use get_nextid() to get a nondecreasing sequence
        $data['sortorder'] = $this->converter->get_nextid();

        if ($data['itemtype'] === 'mod') {
            return $this->process_mod_grade_item($data, $raw);

        } else if (in_array($data['itemtype'], array('manual', 'course', 'category'))) {
            return $this->process_nonmod_grade_item($data, $raw);

        } else {
            $this->log('unsupported grade_item type', backup::LOG_ERROR, $data['itemtype']);
        }
    }

    /**
     * Processes one GRADE_ITEM of the type 'mod'
     */
    protected function process_mod_grade_item(array $data, array $raw) {

        $stashname   = 'gradebook_modgradeitem_'.$data['itemmodule'];
        $stashitemid = $data['iteminstance'];
        $gradeitems  = $this->converter->get_stash_or_default($stashname, $stashitemid, array());

        // typically there will be single item with itemnumber 0
        $gradeitems[$data['itemnumber']] = $data;

        $this->converter->set_stash($stashname, $gradeitems, $stashitemid);

        return $data;
    }

    /**
     * Processes one GRADE_ITEM of te type 'manual' or 'course' or 'category'
     */
    protected function process_nonmod_grade_item(array $data, array $raw) {

        $stashname   = 'gradebook_nonmodgradeitem';
        $stashitemid = $data['id'];
        $this->converter->set_stash($stashname, $data, $stashitemid);

        return $data;
    }

    /**
     * @todo
     */
    public function on_gradebook_grade_item_grades_start() {
    }

    /**
     * Writes the collected information into gradebook.xml
     */
    public function on_gradebook_end() {

        $this->open_xml_writer('gradebook.xml');
        $this->xmlwriter->begin_tag('gradebook');
        $this->write_grade_categories();
        $this->write_grade_items();
        $this->write_grade_letters();
        $this->xmlwriter->end_tag('gradebook');
        $this->close_xml_writer();
    }

    /**
     * Writes grade_categories
     */
    protected function write_grade_categories() {

        $this->xmlwriter->begin_tag('grade_categories');
        foreach ($this->converter->get_stash_itemids('gradebook_gradecategory') as $gradecategoryid) {
            $gradecategory = $this->converter->get_stash('gradebook_gradecategory', $gradecategoryid);
            $path = $this->calculate_category_path($gradecategoryid);
            $gradecategory['depth'] = count($path);
            $gradecategory['path']  = '/'.implode('/', $path).'/';
            $this->write_xml('grade_category', $gradecategory, array('/grade_category/id'));
        }
        $this->xmlwriter->end_tag('grade_categories');
    }

    /**
     * Calculates the path to the grade_category
     *
     * Moodle 1.9 backup does not store the grade_category's depth and path. This method is used
     * to repopulate this information using the $this->categoryparent values.
     *
     * @param int $categoryid
     * @return array of ids including the categoryid
     */
    protected function calculate_category_path($categoryid) {

        if (!array_key_exists($categoryid, $this->categoryparent)) {
            throw new moodle1_convert_exception('gradebook_unknown_categoryid', null, $categoryid);
        }

        $path = array($categoryid);
        $parent = $this->categoryparent[$categoryid];
        while (!is_null($parent)) {
            array_unshift($path, $parent);
            $parent = $this->categoryparent[$parent];
            if (in_array($parent, $path)) {
                throw new moodle1_convert_exception('circular_reference_in_categories_tree');
            }
        }

        return $path;
    }

    /**
     * Writes grade_items
     */
    protected function write_grade_items() {

        $this->xmlwriter->begin_tag('grade_items');
        foreach ($this->converter->get_stash_itemids('gradebook_nonmodgradeitem') as $gradeitemid) {
            $gradeitem = $this->converter->get_stash('gradebook_nonmodgradeitem', $gradeitemid);
            $this->write_xml('grade_item', $gradeitem, array('/grade_item/id'));
        }
        $this->xmlwriter->end_tag('grade_items');
    }

    /**
     * Writes grade_letters
     */
    protected function write_grade_letters() {

        $this->xmlwriter->begin_tag('grade_letters');
        foreach ($this->converter->get_stash_itemids('gradebook_gradeletter') as $gradeletterid) {
            $gradeletter = $this->converter->get_stash('gradebook_gradeletter', $gradeletterid);
            $this->write_xml('grade_letter', $gradeletter, array('/grade_letter/id'));
        }
        $this->xmlwriter->end_tag('grade_letters');
    }
}


/**
 * Shared base class for activity modules, blocks and qtype handlers
 */
abstract class moodle1_plugin_handler extends moodle1_xml_handler {

    /** @var string */
    protected $plugintype;

    /** @var string */
    protected $pluginname;

    /**
     * @param moodle1_converter $converter the converter that requires us
     * @param string $plugintype
     * @param string $pluginname
     */
    public function __construct(moodle1_converter $converter, $plugintype, $pluginname) {

        parent::__construct($converter);
        $this->plugintype = $plugintype;
        $this->pluginname = $pluginname;
    }

    /**
     * Returns the normalized name of the plugin, eg mod_workshop
     *
     * @return string
     */
    public function get_component_name() {
        return $this->plugintype.'_'.$this->pluginname;
    }
}


/**
 * Base class for all question type handlers
 */
abstract class moodle1_qtype_handler extends moodle1_plugin_handler {

    /** @var moodle1_question_bank_handler */
    protected $qbankhandler;

    /**
     * Returns the list of paths within one <QUESTION> that this qtype needs to have included
     * in the grouped question structure
     *
     * @return array of strings
     */
    public function get_question_subpaths() {
        return array();
    }

    /**
     * Gives the qtype handler a chance to write converted data into questions.xml
     *
     * @param array $data grouped question data
     * @param array $raw grouped raw QUESTION data
     */
    public function process_question(array $data, array $raw) {
    }

    /**
     * Converts the answers and writes them into the questions.xml
     *
     * The structure "answers" is used by several qtypes. It contains data from {question_answers} table.
     *
     * @param array $answers as parsed by the grouped parser in moodle.xml
     * @param string $qtype containing the answers
     */
    protected function write_answers(array $answers, $qtype) {

        $this->xmlwriter->begin_tag('answers');
        foreach ($answers as $elementname => $elements) {
            foreach ($elements as $element) {
                $answer = $this->convert_answer($element, $qtype);
                // Migrate images in answertext.
                if ($answer['answerformat'] == FORMAT_HTML) {
                    $answer['answertext'] = $this->migrate_files($answer['answertext'], 'question', 'answer', $answer['id']);
                }
                // Migrate images in feedback.
                if ($answer['feedbackformat'] == FORMAT_HTML) {
                    $answer['feedback'] = $this->migrate_files($answer['feedback'], 'question', 'answerfeedback', $answer['id']);
                }
                $this->write_xml('answer', $answer, array('/answer/id'));
            }
        }
        $this->xmlwriter->end_tag('answers');
    }

    /**
     * Migrate files belonging to one qtype plugin text field.
     *
     * @param array $text the html fragment containing references to files
     * @param string $component the component for restored files
     * @param string $filearea the file area for restored files
     * @param int $itemid the itemid for restored files
     *
     * @return string the text for this field, after files references have been processed
     */
    protected function migrate_files($text, $component, $filearea, $itemid) {
        $context = $this->qbankhandler->get_current_category_context();
        $fileman = $this->qbankhandler->get_file_manager();
        $fileman->contextid = $context['contextid'];
        $fileman->component = $component;
        $fileman->filearea  = $filearea;
        $fileman->itemid    = $itemid;
        $text = moodle1_converter::migrate_referenced_files($text, $fileman);
        return $text;
    }

    /**
     * Writes the grouped numerical_units structure
     *
     * @param array $numericalunits
     */
    protected function write_numerical_units(array $numericalunits) {

        $this->xmlwriter->begin_tag('numerical_units');
        foreach ($numericalunits as $elementname => $elements) {
            foreach ($elements as $element) {
                $element['id'] = $this->converter->get_nextid();
                $this->write_xml('numerical_unit', $element, array('/numerical_unit/id'));
            }
        }
        $this->xmlwriter->end_tag('numerical_units');
    }

    /**
     * Writes the numerical_options structure
     *
     * @see get_default_numerical_options()
     * @param array $numericaloption
     */
    protected function write_numerical_options(array $numericaloption) {

        $this->xmlwriter->begin_tag('numerical_options');
        if (!empty($numericaloption)) {
            $this->write_xml('numerical_option', $numericaloption, array('/numerical_option/id'));
        }
        $this->xmlwriter->end_tag('numerical_options');
    }

    /**
     * Returns default numerical_option structure
     *
     * This structure is not present in moodle.xml, we create a new artificial one here.
     *
     * @see write_numerical_options()
     * @param int $oldquestiontextformat
     * @return array
     */
    protected function get_default_numerical_options($oldquestiontextformat, $units) {
        global $CFG;

        // replay the upgrade step 2009100100 - new table
        $options = array(
            'id'                 => $this->converter->get_nextid(),
            'instructions'       => null,
            'instructionsformat' => 0,
            'showunits'          => 0,
            'unitsleft'          => 0,
            'unitgradingtype'    => 0,
            'unitpenalty'        => 0.1
        );

        // replay the upgrade step 2009100101
        if ($CFG->texteditors !== 'textarea' and $oldquestiontextformat == FORMAT_MOODLE) {
            $options['instructionsformat'] = FORMAT_HTML;
        } else {
            $options['instructionsformat'] = $oldquestiontextformat;
        }

        // Set a good default, depending on whether there are any units defined.
        if (empty($units)) {
            $options['showunits'] = 3;
        }

        return $options;
    }

    /**
     * Writes the dataset_definitions structure
     *
     * @param array $datasetdefinitions array of dataset_definition structures
     */
    protected function write_dataset_definitions(array $datasetdefinitions) {

        $this->xmlwriter->begin_tag('dataset_definitions');
        foreach ($datasetdefinitions as $datasetdefinition) {
            $this->xmlwriter->begin_tag('dataset_definition', array('id' => $this->converter->get_nextid()));
            foreach (array('category', 'name', 'type', 'options', 'itemcount') as $element) {
                $this->xmlwriter->full_tag($element, $datasetdefinition[$element]);
            }
            $this->xmlwriter->begin_tag('dataset_items');
            if (!empty($datasetdefinition['dataset_items']['dataset_item'])) {
                foreach ($datasetdefinition['dataset_items']['dataset_item'] as $datasetitem) {
                    $datasetitem['id'] = $this->converter->get_nextid();
                    $this->write_xml('dataset_item', $datasetitem, array('/dataset_item/id'));
                }
            }
            $this->xmlwriter->end_tag('dataset_items');
            $this->xmlwriter->end_tag('dataset_definition');
        }
        $this->xmlwriter->end_tag('dataset_definitions');
    }

    /// implementation details follow //////////////////////////////////////////

    public function __construct(moodle1_question_bank_handler $qbankhandler, $qtype) {

        parent::__construct($qbankhandler->get_converter(), 'qtype', $qtype);
        $this->qbankhandler = $qbankhandler;
    }

    /**
     * @see self::get_question_subpaths()
     */
    final public function get_paths() {
        throw new moodle1_convert_exception('qtype_handler_get_paths');
    }

    /**
     * Question type handlers cannot open the xml_writer
     */
    final protected function open_xml_writer($filename) {
        throw new moodle1_convert_exception('opening_xml_writer_forbidden');
    }

    /**
     * Question type handlers cannot close the xml_writer
     */
    final protected function close_xml_writer() {
        throw new moodle1_convert_exception('opening_xml_writer_forbidden');
    }

    /**
     * Provides a xml_writer instance to this qtype converter
     *
     * @param xml_writer $xmlwriter
     */
    public function use_xml_writer(xml_writer $xmlwriter) {
        $this->xmlwriter = $xmlwriter;
    }

    /**
     * Converts <ANSWER> structure into the new <answer> one
     *
     * See question_backup_answers() in 1.9 and add_question_question_answers() in 2.0
     *
     * @param array $old the parsed answer array in moodle.xml
     * @param string $qtype the question type the answer is part of
     * @return array
     */
    private function convert_answer(array $old, $qtype) {
        global $CFG;

        $new                    = array();
        $new['id']              = $old['id'];
        $new['answertext']      = $old['answer_text'];
        $new['answerformat']    = 0;   // upgrade step 2010080900
        $new['fraction']        = $old['fraction'];
        $new['feedback']        = $old['feedback'];
        $new['feedbackformat']  = 0;   // upgrade step 2010080900

        // replay upgrade step 2010080901
        if ($qtype !== 'multichoice') {
            $new['answerformat'] = FORMAT_PLAIN;
        } else {
            $new['answertext'] = text_to_html($new['answertext'], false, false, true);
            $new['answerformat'] = FORMAT_HTML;
        }

        if ($CFG->texteditors !== 'textarea') {
            if ($qtype == 'essay') {
                $new['feedback'] = text_to_html($new['feedback'], false, false, true);
            }
            $new['feedbackformat'] = FORMAT_HTML;

        } else {
            $new['feedbackformat'] = FORMAT_MOODLE;
        }

        return $new;
    }
}


/**
 * Base class for activity module handlers
 */
abstract class moodle1_mod_handler extends moodle1_plugin_handler {

    /**
     * Returns the name of the module, eg. 'forum'
     *
     * @return string
     */
    public function get_modname() {
        return $this->pluginname;
    }

    /**
     * Returns course module information for the given instance id
     *
     * The information for this instance id has been stashed by
     * {@link moodle1_course_outline_handler::process_course_module()}
     *
     * @param int $instance the module instance id
     * @param string $modname the module type, defaults to $this->pluginname
     * @return int
     */
    protected function get_cminfo($instance, $modname = null) {

        if (is_null($modname)) {
            $modname = $this->pluginname;
        }
        return $this->converter->get_stash('cminfo_'.$modname, $instance);
    }
}


/**
 * Base class for all modules that are successors of the 1.9 resource module
 */
abstract class moodle1_resource_successor_handler extends moodle1_mod_handler {

    /**
     * Resource successors do not attach to paths themselves, they are called explicitely
     * by moodle1_mod_resource_handler
     *
     * @return array
     */
    final public function get_paths() {
        return array();
    }

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     *
     * Called by {@link moodle1_mod_resource_handler::process_resource()}
     *
     * @param array $data pre-cooked legacy resource data
     * @param array $raw raw legacy resource data
     */
    public function process_legacy_resource(array $data, array $raw = null) {
    }

    /**
     * Called when the parses reaches the end </MOD> resource tag
     *
     * @param array $data the data returned by {@link self::process_resource} or just pre-cooked
     */
    public function on_legacy_resource_end(array $data) {
    }
}

/**
 * Base class for block handlers
 */
abstract class moodle1_block_handler extends moodle1_plugin_handler {

    public function get_paths() {
        $blockname = strtoupper($this->pluginname);
        return array(
            new convert_path('block', "/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/{$blockname}"),
        );
    }

    public function process_block(array $data) {
        $newdata = $this->convert_common_block_data($data);

        $this->write_block_xml($newdata, $data);
        $this->write_inforef_xml($newdata, $data);
        $this->write_roles_xml($newdata, $data);

        return $data;
    }

    protected function convert_common_block_data(array $olddata) {
        $newdata = array();

        $newdata['blockname'] = $olddata['name'];
        $newdata['parentcontextid'] = $this->converter->get_contextid(CONTEXT_COURSE, 0);
        $newdata['showinsubcontexts'] = 0;
        $newdata['pagetypepattern'] = $olddata['pagetype'].='-*';
        $newdata['subpagepattern'] = null;
        $newdata['defaultregion'] = ($olddata['position']=='l')?'side-pre':'side-post';
        $newdata['defaultweight'] = $olddata['weight'];
        $newdata['configdata'] = $this->convert_configdata($olddata);

        return $newdata;
    }

    protected function convert_configdata(array $olddata) {
        return $olddata['configdata'];
    }

    protected function write_block_xml($newdata, $data) {
        $contextid = $this->converter->get_contextid(CONTEXT_BLOCK, $data['id']);

        $this->open_xml_writer("course/blocks/{$data['name']}_{$data['id']}/block.xml");
        $this->xmlwriter->begin_tag('block', array('id' => $data['id'], 'contextid' => $contextid));

        foreach ($newdata as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        $this->xmlwriter->begin_tag('block_positions');
        $this->xmlwriter->begin_tag('block_position', array('id' => 1));
        $this->xmlwriter->full_tag('contextid', $newdata['parentcontextid']);
        $this->xmlwriter->full_tag('pagetype', $data['pagetype']);
        $this->xmlwriter->full_tag('subpage', '');
        $this->xmlwriter->full_tag('visible', $data['visible']);
        $this->xmlwriter->full_tag('region', $newdata['defaultregion']);
        $this->xmlwriter->full_tag('weight', $newdata['defaultweight']);
        $this->xmlwriter->end_tag('block_position');
        $this->xmlwriter->end_tag('block_positions');
        $this->xmlwriter->end_tag('block');
        $this->close_xml_writer();
    }

    protected function write_inforef_xml($newdata, $data) {
        $this->open_xml_writer("course/blocks/{$data['name']}_{$data['id']}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        // Subclasses may provide inforef contents if needed
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }

    protected function write_roles_xml($newdata, $data) {
        // This is an empty shell, as the moodle1 converter doesn't handle user data.
        $this->open_xml_writer("course/blocks/{$data['name']}_{$data['id']}/roles.xml");
        $this->xmlwriter->begin_tag('roles');
        $this->xmlwriter->full_tag('role_overrides', '');
        $this->xmlwriter->full_tag('role_assignments', '');
        $this->xmlwriter->end_tag('roles');
        $this->close_xml_writer();
    }
}


/**
 * Base class for block generic handler
 */
class moodle1_block_generic_handler extends moodle1_block_handler {

}

/**
 * Base class for the activity modules' subplugins
 */
abstract class moodle1_submod_handler extends moodle1_plugin_handler {

    /** @var moodle1_mod_handler */
    protected $parenthandler;

    /**
     * @param moodle1_mod_handler $parenthandler the handler of a module we are subplugin of
     * @param string $subplugintype the type of the subplugin
     * @param string $subpluginname the name of the subplugin
     */
    public function __construct(moodle1_mod_handler $parenthandler, $subplugintype, $subpluginname) {
        $this->parenthandler = $parenthandler;
        parent::__construct($parenthandler->converter, $subplugintype, $subpluginname);
    }

    /**
     * Activity module subplugins can't declare any paths to handle
     *
     * The paths must be registered by the parent module and then re-dispatched to the
     * relevant subplugins for eventual processing.
     *
     * @return array empty array
     */
    final public function get_paths() {
        return array();
    }
}
