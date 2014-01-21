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
 * Provides classes used by the moodle1 converter
 *
 * @package    backup-convert
 * @subpackage moodle1
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/converter/convertlib.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/progressive_parser.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/grouped_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/restore_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/xml/contenttransformer/xml_contenttransformer.class.php');
require_once(dirname(__FILE__) . '/handlerlib.php');

/**
 * Converter of Moodle 1.9 backup into Moodle 2.x format
 */
class moodle1_converter extends base_converter {

    /** @var progressive_parser moodle.xml file parser */
    protected $xmlparser;

    /** @var moodle1_parser_processor */
    protected $xmlprocessor;

    /** @var array of {@link convert_path} to process */
    protected $pathelements = array();

    /** @var null|string the current module being processed - used to expand the MOD paths */
    protected $currentmod = null;

    /** @var null|string the current block being processed - used to expand the BLOCK paths */
    protected $currentblock = null;

    /** @var string path currently locking processing of children */
    protected $pathlock;

    /** @var int used by the serial number {@link get_nextid()} */
    private $nextid = 1;

    /**
     * Instructs the dispatcher to ignore all children below path processor returning it
     */
    const SKIP_ALL_CHILDREN = -991399;

    /**
     * Log a message
     *
     * @see parent::log()
     * @param string $message message text
     * @param int $level message level {@example backup::LOG_WARNING}
     * @param null|mixed $a additional information
     * @param null|int $depth the message depth
     * @param bool $display whether the message should be sent to the output, too
     */
    public function log($message, $level, $a = null, $depth = null, $display = false) {
        parent::log('(moodle1) '.$message, $level, $a, $depth, $display);
    }

    /**
     * Detects the Moodle 1.9 format of the backup directory
     *
     * @param string $tempdir the name of the backup directory
     * @return null|string backup::FORMAT_MOODLE1 if the Moodle 1.9 is detected, null otherwise
     */
    public static function detect_format($tempdir) {
        global $CFG;

        $filepath = $CFG->tempdir . '/backup/' . $tempdir . '/moodle.xml';
        if (file_exists($filepath)) {
            // looks promising, lets load some information
            $handle = fopen($filepath, 'r');
            $first_chars = fread($handle, 200);
            fclose($handle);

            // check if it has the required strings
            if (strpos($first_chars,'<?xml version="1.0" encoding="UTF-8"?>') !== false and
                strpos($first_chars,'<MOODLE_BACKUP>') !== false and
                strpos($first_chars,'<INFO>') !== false) {

                return backup::FORMAT_MOODLE1;
            }
        }

        return null;
    }

    /**
     * Initialize the instance if needed, called by the constructor
     *
     * Here we create objects we need before the execution.
     */
    protected function init() {

        // ask your mother first before going out playing with toys
        parent::init();

        $this->log('initializing '.$this->get_name().' converter', backup::LOG_INFO);

        // good boy, prepare XML parser and processor
        $this->log('setting xml parser', backup::LOG_DEBUG, null, 1);
        $this->xmlparser = new progressive_parser();
        $this->xmlparser->set_file($this->get_tempdir_path() . '/moodle.xml');
        $this->log('setting xml processor', backup::LOG_DEBUG, null, 1);
        $this->xmlprocessor = new moodle1_parser_processor($this);
        $this->xmlparser->set_processor($this->xmlprocessor);

        // make sure that MOD and BLOCK paths are visited
        $this->xmlprocessor->add_path('/MOODLE_BACKUP/COURSE/MODULES/MOD');
        $this->xmlprocessor->add_path('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK');

        // register the conversion handlers
        foreach (moodle1_handlers_factory::get_handlers($this) as $handler) {
            $this->log('registering handler', backup::LOG_DEBUG, get_class($handler), 1);
            $this->register_handler($handler, $handler->get_paths());
        }
    }

    /**
     * Converts the contents of the tempdir into the target format in the workdir
     */
    protected function execute() {
        $this->log('creating the stash storage', backup::LOG_DEBUG);
        $this->create_stash_storage();

        $this->log('parsing moodle.xml starts', backup::LOG_DEBUG);
        $this->xmlparser->process();
        $this->log('parsing moodle.xml done', backup::LOG_DEBUG);

        $this->log('dropping the stash storage', backup::LOG_DEBUG);
        $this->drop_stash_storage();
    }

    /**
     * Register a handler for the given path elements
     */
    protected function register_handler(moodle1_handler $handler, array $elements) {

        // first iteration, push them to new array, indexed by name
        // to detect duplicates in names or paths
        $names = array();
        $paths = array();
        foreach($elements as $element) {
            if (!$element instanceof convert_path) {
                throw new convert_exception('path_element_wrong_class', get_class($element));
            }
            if (array_key_exists($element->get_name(), $names)) {
                throw new convert_exception('path_element_name_alreadyexists', $element->get_name());
            }
            if (array_key_exists($element->get_path(), $paths)) {
                throw new convert_exception('path_element_path_alreadyexists', $element->get_path());
            }
            $names[$element->get_name()] = true;
            $paths[$element->get_path()] = $element;
        }

        // now, for each element not having a processing object yet, assign the handler
        // if the element is not a memeber of a group
        foreach($paths as $key => $element) {
            if (is_null($element->get_processing_object()) and !$this->grouped_parent_exists($element, $paths)) {
                $paths[$key]->set_processing_object($handler);
            }
            // add the element path to the processor
            $this->xmlprocessor->add_path($element->get_path(), $element->is_grouped());
        }

        // done, store the paths (duplicates by path are discarded)
        $this->pathelements = array_merge($this->pathelements, $paths);

        // remove the injected plugin name element from the MOD and BLOCK paths
        // and register such collapsed path, too
        foreach ($elements as $element) {
            $path = $element->get_path();
            $path = preg_replace('/^\/MOODLE_BACKUP\/COURSE\/MODULES\/MOD\/(\w+)\//', '/MOODLE_BACKUP/COURSE/MODULES/MOD/', $path);
            $path = preg_replace('/^\/MOODLE_BACKUP\/COURSE\/BLOCKS\/BLOCK\/(\w+)\//', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/', $path);
            if (!empty($path) and $path != $element->get_path()) {
                $this->xmlprocessor->add_path($path, false);
            }
        }
    }

    /**
     * Helper method used by {@link self::register_handler()}
     *
     * @param convert_path $pelement path element
     * @param array of convert_path instances
     * @return bool true if grouped parent was found, false otherwise
     */
    protected function grouped_parent_exists($pelement, $elements) {

        foreach ($elements as $element) {
            if ($pelement->get_path() == $element->get_path()) {
                // don't compare against itself
                continue;
            }
            // if the element is grouped and it is a parent of pelement, return true
            if ($element->is_grouped() and strpos($pelement->get_path() .  '/', $element->get_path()) === 0) {
                return true;
            }
        }

        // no grouped parent found
        return false;
    }

    /**
     * Process the data obtained from the XML parser processor
     *
     * This methods receives one chunk of information from the XML parser
     * processor and dispatches it, following the naming rules.
     * We are expanding the modules and blocks paths here to include the plugin's name.
     *
     * @param array $data
     */
    public function process_chunk($data) {

        $path = $data['path'];

        // expand the MOD paths so that they contain the module name
        if ($path === '/MOODLE_BACKUP/COURSE/MODULES/MOD') {
            $this->currentmod = strtoupper($data['tags']['MODTYPE']);
            $path = '/MOODLE_BACKUP/COURSE/MODULES/MOD/' . $this->currentmod;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/MODULES/MOD') === 0) {
            $path = str_replace('/MOODLE_BACKUP/COURSE/MODULES/MOD', '/MOODLE_BACKUP/COURSE/MODULES/MOD/' . $this->currentmod, $path);
        }

        // expand the BLOCK paths so that they contain the module name
        if ($path === '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') {
            $this->currentblock = strtoupper($data['tags']['NAME']);
            $path = '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentblock;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') === 0) {
            $path = str_replace('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentblock, $path);
        }

        if ($path !== $data['path']) {
            if (!array_key_exists($path, $this->pathelements)) {
                // no handler registered for the transformed MOD or BLOCK path
                $this->log('no handler attached', backup::LOG_WARNING, $path);
                return;

            } else {
                // pretend as if the original $data contained the tranformed path
                $data['path'] = $path;
            }
        }

        if (!array_key_exists($data['path'], $this->pathelements)) {
            // path added to the processor without the handler
            throw new convert_exception('missing_path_handler', $data['path']);
        }

        $element  = $this->pathelements[$data['path']];
        $object   = $element->get_processing_object();
        $method   = $element->get_processing_method();
        $returned = null; // data returned by the processing method, if any

        if (empty($object)) {
            throw new convert_exception('missing_processing_object', null, $data['path']);
        }

        // release the lock if we aren't anymore within children of it
        if (!is_null($this->pathlock) and strpos($data['path'], $this->pathlock) === false) {
            $this->pathlock = null;
        }

        // if the path is not locked, apply the element's recipes and dispatch
        // the cooked tags to the processing method
        if (is_null($this->pathlock)) {
            $rawdatatags  = $data['tags'];
            $data['tags'] = $element->apply_recipes($data['tags']);

            // if the processing method exists, give it a chance to modify data
            if (method_exists($object, $method)) {
                $returned = $object->$method($data['tags'], $rawdatatags);
            }
        }

        // if the dispatched method returned SKIP_ALL_CHILDREN, remember the current path
        // and lock it so that its children are not dispatched
        if ($returned === self::SKIP_ALL_CHILDREN) {
            // check we haven't any previous lock
            if (!is_null($this->pathlock)) {
                throw new convert_exception('already_locked_path', $data['path']);
            }
            // set the lock - nothing below the current path will be dispatched
            $this->pathlock = $data['path'] . '/';

        // if the method has returned any info, set element data to it
        } else if (!is_null($returned)) {
            $element->set_tags($returned);

        // use just the cooked parsed data otherwise
        } else {
            $element->set_tags($data['tags']);
        }
    }

    /**
     * Executes operations required at the start of a watched path
     *
     * For MOD and BLOCK paths, this is supported only for the sub-paths, not the root
     * module/block element. For the illustration:
     *
     * You CAN'T attach on_xxx_start() listener to a path like
     * /MOODLE_BACKUP/COURSE/MODULES/MOD/WORKSHOP because the <MOD> must
     * be processed first in {@link self::process_chunk()} where $this->currentmod
     * is set.
     *
     * You CAN attach some on_xxx_start() listener to a path like
     * /MOODLE_BACKUP/COURSE/MODULES/MOD/WORKSHOP/SUBMISSIONS because it is
     * a sub-path under <MOD> and we have $this->currentmod already set when the
     * <SUBMISSIONS> is reached.
     *
     * @param string $path in the original file
     */
    public function path_start_reached($path) {

        if ($path === '/MOODLE_BACKUP/COURSE/MODULES/MOD') {
            $this->currentmod = null;
            $forbidden = true;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/MODULES/MOD') === 0) {
            // expand the MOD paths so that they contain the module name
            $path = str_replace('/MOODLE_BACKUP/COURSE/MODULES/MOD', '/MOODLE_BACKUP/COURSE/MODULES/MOD/' . $this->currentmod, $path);
        }

        if ($path === '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') {
            $this->currentblock = null;
            $forbidden = true;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') === 0) {
            // expand the BLOCK paths so that they contain the module name
            $path = str_replace('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentblock, $path);
        }

        if (empty($this->pathelements[$path])) {
            return;
        }

        $element = $this->pathelements[$path];
        $pobject = $element->get_processing_object();
        $method  = $element->get_start_method();

        if (method_exists($pobject, $method)) {
            if (empty($forbidden)) {
                $pobject->$method();

            } else {
                // this path is not supported because we do not know the module/block yet
                throw new coding_exception('Attaching the on-start event listener to the root MOD or BLOCK element is forbidden.');
            }
        }
    }

    /**
     * Executes operations required at the end of a watched path
     *
     * @param string $path in the original file
     */
    public function path_end_reached($path) {

        // expand the MOD paths so that they contain the current module name
        if ($path === '/MOODLE_BACKUP/COURSE/MODULES/MOD') {
            $path = '/MOODLE_BACKUP/COURSE/MODULES/MOD/' . $this->currentmod;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/MODULES/MOD') === 0) {
            $path = str_replace('/MOODLE_BACKUP/COURSE/MODULES/MOD', '/MOODLE_BACKUP/COURSE/MODULES/MOD/' . $this->currentmod, $path);
        }

        // expand the BLOCK paths so that they contain the module name
        if ($path === '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') {
            $path = '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentblock;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK') === 0) {
            $path = str_replace('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentblock, $path);
        }

        if (empty($this->pathelements[$path])) {
            return;
        }

        $element = $this->pathelements[$path];
        $pobject = $element->get_processing_object();
        $method  = $element->get_end_method();
        $tags    = $element->get_tags();

        if (method_exists($pobject, $method)) {
            $pobject->$method($tags);
        }
    }

    /**
     * Creates the temporary storage for stashed data
     *
     * This implementation uses backup_ids_temp table.
     */
    public function create_stash_storage() {
        backup_controller_dbops::create_backup_ids_temp_table($this->get_id());
    }

    /**
     * Drops the temporary storage of stashed data
     *
     * This implementation uses backup_ids_temp table.
     */
    public function drop_stash_storage() {
        backup_controller_dbops::drop_backup_ids_temp_table($this->get_id());
    }

    /**
     * Stores some information for later processing
     *
     * This implementation uses backup_ids_temp table to store data. Make
     * sure that the $stashname + $itemid combo is unique.
     *
     * @param string $stashname name of the stash
     * @param mixed $info information to stash
     * @param int $itemid optional id for multiple infos within the same stashname
     */
    public function set_stash($stashname, $info, $itemid = 0) {
        try {
            restore_dbops::set_backup_ids_record($this->get_id(), $stashname, $itemid, 0, null, $info);

        } catch (dml_exception $e) {
            throw new moodle1_convert_storage_exception('unable_to_restore_stash', null, $e->getMessage());
        }
    }

    /**
     * Restores a given stash stored previously by {@link self::set_stash()}
     *
     * @param string $stashname name of the stash
     * @param int $itemid optional id for multiple infos within the same stashname
     * @throws moodle1_convert_empty_storage_exception if the info has not been stashed previously
     * @return mixed stashed data
     */
    public function get_stash($stashname, $itemid = 0) {

        $record = restore_dbops::get_backup_ids_record($this->get_id(), $stashname, $itemid);

        if (empty($record)) {
            throw new moodle1_convert_empty_storage_exception('required_not_stashed_data', array($stashname, $itemid));
        } else {
            return $record->info;
        }
    }

    /**
     * Restores a given stash or returns the given default if there is no such stash
     *
     * @param string $stashname name of the stash
     * @param int $itemid optional id for multiple infos within the same stashname
     * @param mixed $default information to return if the info has not been stashed previously
     * @return mixed stashed data or the default value
     */
    public function get_stash_or_default($stashname, $itemid = 0, $default = null) {
        try {
            return $this->get_stash($stashname, $itemid);
        } catch (moodle1_convert_empty_storage_exception $e) {
            return $default;
        }
    }

    /**
     * Returns the list of existing stashes
     *
     * @return array
     */
    public function get_stash_names() {
        global $DB;

        $search = array(
            'backupid' => $this->get_id(),
        );

        return array_keys($DB->get_records('backup_ids_temp', $search, '', 'itemname'));
    }

    /**
     * Returns the list of stashed $itemids in the given stash
     *
     * @param string $stashname
     * @return array
     */
    public function get_stash_itemids($stashname) {
        global $DB;

        $search = array(
            'backupid' => $this->get_id(),
            'itemname' => $stashname
        );

        return array_keys($DB->get_records('backup_ids_temp', $search, '', 'itemid'));
    }

    /**
     * Generates an artificial context id
     *
     * Moodle 1.9 backups do not contain any context information. But we need them
     * in Moodle 2.x format so here we generate fictive context id for every given
     * context level + instance combo.
     *
     * CONTEXT_SYSTEM and CONTEXT_COURSE ignore the $instance as they represent a
     * single system or the course being restored.
     *
     * @see context_system::instance()
     * @see context_course::instance()
     * @param int $level the context level, like CONTEXT_COURSE or CONTEXT_MODULE
     * @param int $instance the instance id, for example $course->id for courses or $cm->id for activity modules
     * @return int the context id
     */
    public function get_contextid($level, $instance = 0) {

        $stashname = 'context' . $level;

        if ($level == CONTEXT_SYSTEM or $level == CONTEXT_COURSE) {
            $instance = 0;
        }

        try {
            // try the previously stashed id
            return $this->get_stash($stashname, $instance);

        } catch (moodle1_convert_empty_storage_exception $e) {
            // this context level + instance is required for the first time
            $newid = $this->get_nextid();
            $this->set_stash($stashname, $newid, $instance);
            return $newid;
        }
    }

    /**
     * Simple autoincrement generator
     *
     * @return int the next number in a row of numbers
     */
    public function get_nextid() {
        return $this->nextid++;
    }

    /**
     * Creates and returns new instance of the file manager
     *
     * @param int $contextid the default context id of the files being migrated
     * @param string $component the default component name of the files being migrated
     * @param string $filearea the default file area of the files being migrated
     * @param int $itemid the default item id of the files being migrated
     * @param int $userid initial user id of the files being migrated
     * @return moodle1_file_manager
     */
    public function get_file_manager($contextid = null, $component = null, $filearea = null, $itemid = 0, $userid = null) {
        return new moodle1_file_manager($this, $contextid, $component, $filearea, $itemid, $userid);
    }

    /**
     * Creates and returns new instance of the inforef manager
     *
     * @param string $name the name of the annotator (like course, section, activity, block)
     * @param int $id the id of the annotator if required
     * @return moodle1_inforef_manager
     */
    public function get_inforef_manager($name, $id = 0) {
        return new moodle1_inforef_manager($this, $name, $id);
    }


    /**
     * Migrates all course files referenced from the hypertext using the given filemanager
     *
     * This is typically used to convert images embedded into the intro fields.
     *
     * @param string $text hypertext containing $@FILEPHP@$ referenced
     * @param moodle1_file_manager $fileman file manager to use for the file migration
     * @return string the original $text with $@FILEPHP@$ references replaced with the new @@PLUGINFILE@@
     */
    public static function migrate_referenced_files($text, moodle1_file_manager $fileman) {

        $files = self::find_referenced_files($text);
        if (!empty($files)) {
            foreach ($files as $file) {
                try {
                    $fileman->migrate_file('course_files'.$file, dirname($file));
                } catch (moodle1_convert_exception $e) {
                    // file probably does not exist
                    $fileman->log('error migrating file', backup::LOG_WARNING, 'course_files'.$file);
                }
            }
            $text = self::rewrite_filephp_usage($text, $files);
        }

        return $text;
    }

    /**
     * Detects all links to file.php encoded via $@FILEPHP@$ and returns the files to migrate
     *
     * @see self::migrate_referenced_files()
     * @param string $text
     * @return array
     */
    public static function find_referenced_files($text) {

        $files = array();

        if (empty($text) or is_numeric($text)) {
            return $files;
        }

        $matches = array();
        $pattern = '|(["\'])(\$@FILEPHP@\$.+?)\1|';
        $result = preg_match_all($pattern, $text, $matches);
        if ($result === false) {
            throw new moodle1_convert_exception('error_while_searching_for_referenced_files');
        }
        if ($result == 0) {
            return $files;
        }
        foreach ($matches[2] as $match) {
            $file = str_replace(array('$@FILEPHP@$', '$@SLASH@$', '$@FORCEDOWNLOAD@$'), array('', '/', ''), $match);
            if ($file === clean_param($file, PARAM_PATH)) {
                $files[] = rawurldecode($file);
            }
        }

        return array_unique($files);
    }

    /**
     * Given the list of migrated files, rewrites references to them from $@FILEPHP@$ form to the @@PLUGINFILE@@ one
     *
     * @see self::migrate_referenced_files()
     * @param string $text
     * @param array $files
     * @return string
     */
    public static function rewrite_filephp_usage($text, array $files) {

        foreach ($files as $file) {
            // Expect URLs properly encoded by default.
            $parts   = explode('/', $file);
            $encoded = implode('/', array_map('rawurlencode', $parts));
            $fileref = '$@FILEPHP@$'.str_replace('/', '$@SLASH@$', $encoded);
            $text    = str_replace($fileref.'$@FORCEDOWNLOAD@$', '@@PLUGINFILE@@'.$encoded.'?forcedownload=1', $text);
            $text    = str_replace($fileref, '@@PLUGINFILE@@'.$encoded, $text);
            // Add support for URLs without any encoding.
            $fileref = '$@FILEPHP@$'.str_replace('/', '$@SLASH@$', $file);
            $text    = str_replace($fileref.'$@FORCEDOWNLOAD@$', '@@PLUGINFILE@@'.$encoded.'?forcedownload=1', $text);
            $text    = str_replace($fileref, '@@PLUGINFILE@@'.$encoded, $text);
        }

        return $text;
    }

    /**
     * @see parent::description()
     */
    public static function description() {

        return array(
            'from'  => backup::FORMAT_MOODLE1,
            'to'    => backup::FORMAT_MOODLE,
            'cost'  => 10,
        );
    }
}


/**
 * Exception thrown by this converter
 */
class moodle1_convert_exception extends convert_exception {
}


/**
 * Exception thrown by the temporary storage subsystem of moodle1_converter
 */
class moodle1_convert_storage_exception extends moodle1_convert_exception {
}


/**
 * Exception thrown by the temporary storage subsystem of moodle1_converter
 */
class moodle1_convert_empty_storage_exception extends moodle1_convert_exception {
}


/**
 * XML parser processor used for processing parsed moodle.xml
 */
class moodle1_parser_processor extends grouped_parser_processor {

    /** @var moodle1_converter */
    protected $converter;

    public function __construct(moodle1_converter $converter) {
        $this->converter = $converter;
        parent::__construct();
    }

    /**
     * Provides NULL decoding
     *
     * Note that we do not decode $@FILEPHP@$ and friends here as we are going to write them
     * back immediately into another XML file.
     */
    public function process_cdata($cdata) {

        if ($cdata === '$@NULL@$') {
            return null;
        }

        return $cdata;
    }

    /**
     * Dispatches the data chunk to the converter class
     *
     * @param array $data the chunk of parsed data
     */
    protected function dispatch_chunk($data) {
        $this->converter->process_chunk($data);
    }

    /**
     * Informs the converter at the start of a watched path
     *
     * @param string $path
     */
    protected function notify_path_start($path) {
        $this->converter->path_start_reached($path);
    }

    /**
     * Informs the converter at the end of a watched path
     *
     * @param string $path
     */
    protected function notify_path_end($path) {
        $this->converter->path_end_reached($path);
    }
}


/**
 * XML transformer that modifies the content of the files being written during the conversion
 *
 * @see backup_xml_transformer
 */
class moodle1_xml_transformer extends xml_contenttransformer {

    /**
     * Modify the content before it is writter to a file
     *
     * @param string|mixed $content
     */
    public function process($content) {

        // the content should be a string. If array or object is given, try our best recursively
        // but inform the developer
        if (is_array($content)) {
            debugging('Moodle1 XML transformer should not process arrays but plain content always', DEBUG_DEVELOPER);
            foreach($content as $key => $plaincontent) {
                $content[$key] = $this->process($plaincontent);
            }
            return $content;

        } else if (is_object($content)) {
            debugging('Moodle1 XML transformer should not process objects but plain content always', DEBUG_DEVELOPER);
            foreach((array)$content as $key => $plaincontent) {
                $content[$key] = $this->process($plaincontent);
            }
            return (object)$content;
        }

        // try to deal with some trivial cases first
        if (is_null($content)) {
            return '$@NULL@$';

        } else if ($content === '') {
            return '';

        } else if (is_numeric($content)) {
            return $content;

        } else if (strlen($content) < 32) {
            return $content;
        }

        return $content;
    }
}


/**
 * Class representing a path to be converted from XML file
 *
 * This was created as a copy of {@link restore_path_element} and should be refactored
 * probably.
 */
class convert_path {

    /** @var string name of the element */
    protected $name;

    /** @var string path within the XML file this element will handle */
    protected $path;

    /** @var bool flag to define if this element will get child ones grouped or no */
    protected $grouped;

    /** @var object object instance in charge of processing this element. */
    protected $pobject = null;

    /** @var string the name of the processing method */
    protected $pmethod = null;

    /** @var string the name of the path start event handler */
    protected $smethod = null;

    /** @var string the name of the path end event handler */
    protected $emethod = null;

    /** @var mixed last data read for this element or returned data by processing method */
    protected $tags = null;

    /** @var array of deprecated fields that are dropped */
    protected $dropfields = array();

    /** @var array of fields renaming */
    protected $renamefields = array();

    /** @var array of new fields to add and their initial values */
    protected $newfields = array();

    /**
     * Constructor
     *
     * The optional recipe array can have three keys, and for each key, the value is another array.
     * - newfields    => array fieldname => defaultvalue indicates fields that have been added to the table,
     *                                                   and so should be added to the XML.
     * - dropfields   => array fieldname                 indicates fieldsthat have been dropped from the table,
     *                                                   and so can be dropped from the XML.
     * - renamefields => array oldname => newname        indicates fieldsthat have been renamed in the table,
     *                                                   and so should be renamed in the XML.
     * {@line moodle1_course_outline_handler} is a good example that uses all of these.
     *
     * @param string $name name of the element
     * @param string $path path of the element
     * @param array $recipe basic description of the structure conversion
     * @param bool $grouped to gather information in grouped mode or no
     */
    public function __construct($name, $path, array $recipe = array(), $grouped = false) {

        $this->validate_name($name);

        $this->name     = $name;
        $this->path     = $path;
        $this->grouped  = $grouped;

        // set the default method names
        $this->set_processing_method('process_' . $name);
        $this->set_start_method('on_'.$name.'_start');
        $this->set_end_method('on_'.$name.'_end');

        if ($grouped and !empty($recipe)) {
            throw new convert_path_exception('recipes_not_supported_for_grouped_elements');
        }

        if (isset($recipe['dropfields']) and is_array($recipe['dropfields'])) {
            $this->set_dropped_fields($recipe['dropfields']);
        }
        if (isset($recipe['renamefields']) and is_array($recipe['renamefields'])) {
            $this->set_renamed_fields($recipe['renamefields']);
        }
        if (isset($recipe['newfields']) and is_array($recipe['newfields'])) {
            $this->set_new_fields($recipe['newfields']);
        }
    }

    /**
     * Validates and sets the given processing object
     *
     * @param object $pobject processing object, must provide a method to be called
     */
    public function set_processing_object($pobject) {
        $this->validate_pobject($pobject);
        $this->pobject = $pobject;
    }

    /**
     * Sets the name of the processing method
     *
     * @param string $pmethod
     */
    public function set_processing_method($pmethod) {
        $this->pmethod = $pmethod;
    }

    /**
     * Sets the name of the path start event listener
     *
     * @param string $smethod
     */
    public function set_start_method($smethod) {
        $this->smethod = $smethod;
    }

    /**
     * Sets the name of the path end event listener
     *
     * @param string $emethod
     */
    public function set_end_method($emethod) {
        $this->emethod = $emethod;
    }

    /**
     * Sets the element tags
     *
     * @param array $tags
     */
    public function set_tags($tags) {
        $this->tags = $tags;
    }

    /**
     * Sets the list of deprecated fields to drop
     *
     * @param array $fields
     */
    public function set_dropped_fields(array $fields) {
        $this->dropfields = $fields;
    }

    /**
     * Sets the required new names of the current fields
     *
     * @param array $fields (string)$currentname => (string)$newname
     */
    public function set_renamed_fields(array $fields) {
        $this->renamefields = $fields;
    }

    /**
     * Sets the new fields and their values
     *
     * @param array $fields (string)$field => (mixed)value
     */
    public function set_new_fields(array $fields) {
        $this->newfields = $fields;
    }

    /**
     * Cooks the parsed tags data by applying known recipes
     *
     * Recipes are used for common trivial operations like adding new fields
     * or renaming fields. The handler's processing method receives cooked
     * data.
     *
     * @param array $data the contents of the element
     * @return array
     */
    public function apply_recipes(array $data) {

        $cooked = array();

        foreach ($data as $name => $value) {
            // lower case rocks!
            $name = strtolower($name);

            if (is_array($value)) {
                if ($this->is_grouped()) {
                    $value = $this->apply_recipes($value);
                } else {
                    throw new convert_path_exception('non_grouped_path_with_array_values');
                }
            }

            // drop legacy fields
            if (in_array($name, $this->dropfields)) {
                continue;
            }

            // fields renaming
            if (array_key_exists($name, $this->renamefields)) {
                $name = $this->renamefields[$name];
            }

            $cooked[$name] = $value;
        }

        // adding new fields
        foreach ($this->newfields as $name => $value) {
            $cooked[$name] = $value;
        }

        return $cooked;
    }

    /**
     * @return string the element given name
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @return string the path to the element
     */
    public function get_path() {
        return $this->path;
    }

    /**
     * @return bool flag to define if this element will get child ones grouped or no
     */
    public function is_grouped() {
        return $this->grouped;
    }

    /**
     * @return object the processing object providing the processing method
     */
    public function get_processing_object() {
        return $this->pobject;
    }

    /**
     * @return string the name of the method to call to process the element
     */
    public function get_processing_method() {
        return $this->pmethod;
    }

    /**
     * @return string the name of the path start event listener
     */
    public function get_start_method() {
        return $this->smethod;
    }

    /**
     * @return string the name of the path end event listener
     */
    public function get_end_method() {
        return $this->emethod;
    }

    /**
     * @return mixed the element data
     */
    public function get_tags() {
        return $this->tags;
    }


    /// end of public API //////////////////////////////////////////////////////

    /**
     * Makes sure the given name is a valid element name
     *
     * Note it may look as if we used exceptions for code flow control here. That's not the case
     * as we actually validate the code, not the user data. And the code is supposed to be
     * correct.
     *
     * @param string @name the element given name
     * @throws convert_path_exception
     * @return void
     */
    protected function validate_name($name) {
        // Validate various name constraints, throwing exception if needed
        if (empty($name)) {
            throw new convert_path_exception('convert_path_emptyname', $name);
        }
        if (preg_replace('/\s/', '', $name) != $name) {
            throw new convert_path_exception('convert_path_whitespace', $name);
        }
        if (preg_replace('/[^\x30-\x39\x41-\x5a\x5f\x61-\x7a]/', '', $name) != $name) {
            throw new convert_path_exception('convert_path_notasciiname', $name);
        }
    }

    /**
     * Makes sure that the given object is a valid processing object
     *
     * The processing object must be an object providing at least element's processing method
     * or path-reached-end event listener or path-reached-start listener method.
     *
     * Note it may look as if we used exceptions for code flow control here. That's not the case
     * as we actually validate the code, not the user data. And the code is supposed to be
     * correct.
      *
     * @param object $pobject
     * @throws convert_path_exception
     * @return void
     */
    protected function validate_pobject($pobject) {
        if (!is_object($pobject)) {
            throw new convert_path_exception('convert_path_no_object', get_class($pobject));
        }
        if (!method_exists($pobject, $this->get_processing_method()) and
            !method_exists($pobject, $this->get_end_method()) and
            !method_exists($pobject, $this->get_start_method())) {
            throw new convert_path_exception('convert_path_missing_method', get_class($pobject));
        }
    }
}


/**
 * Exception being thrown by {@link convert_path} methods
 */
class convert_path_exception extends moodle_exception {

    /**
     * Constructor
     *
     * @param string $errorcode key for the corresponding error string
     * @param mixed $a extra words and phrases that might be required by the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}


/**
 * The class responsible for files migration
 *
 * The files in Moodle 1.9 backup are stored in moddata, user_files, group_files,
 * course_files and site_files folders.
 */
class moodle1_file_manager implements loggable {

    /** @var moodle1_converter instance we serve to */
    public $converter;

    /** @var int context id of the files being migrated */
    public $contextid;

    /** @var string component name of the files being migrated */
    public $component;

    /** @var string file area of the files being migrated */
    public $filearea;

    /** @var int item id of the files being migrated */
    public $itemid = 0;

    /** @var int user id */
    public $userid;

    /** @var string the root of the converter temp directory */
    protected $basepath;

    /** @var array of file ids that were migrated by this instance */
    protected $fileids = array();

    /**
     * Constructor optionally accepting some default values for the migrated files
     *
     * @param moodle1_converter $converter the converter instance we serve to
     * @param int $contextid initial context id of the files being migrated
     * @param string $component initial component name of the files being migrated
     * @param string $filearea initial file area of the files being migrated
     * @param int $itemid initial item id of the files being migrated
     * @param int $userid initial user id of the files being migrated
     */
    public function __construct(moodle1_converter $converter, $contextid = null, $component = null, $filearea = null, $itemid = 0, $userid = null) {
        // set the initial destination of the migrated files
        $this->converter = $converter;
        $this->contextid = $contextid;
        $this->component = $component;
        $this->filearea  = $filearea;
        $this->itemid    = $itemid;
        $this->userid    = $userid;
        // set other useful bits
        $this->basepath  = $converter->get_tempdir_path();
    }

    /**
     * Migrates one given file stored on disk
     *
     * @param string $sourcepath the path to the source local file within the backup archive {@example 'moddata/foobar/file.ext'}
     * @param string $filepath the file path of the migrated file, defaults to the root directory '/' {@example '/sub/dir/'}
     * @param string $filename the name of the migrated file, defaults to the same as the source file has
     * @param int $sortorder the sortorder of the file (main files have sortorder set to 1)
     * @param int $timecreated override the timestamp of when the migrated file should appear as created
     * @param int $timemodified override the timestamp of when the migrated file should appear as modified
     * @return int id of the migrated file
     */
    public function migrate_file($sourcepath, $filepath = '/', $filename = null, $sortorder = 0, $timecreated = null, $timemodified = null) {

        // Normalise Windows paths a bit.
        $sourcepath = str_replace('\\', '/', $sourcepath);

        // PARAM_PATH must not be used on full OS path!
        if ($sourcepath !== clean_param($sourcepath, PARAM_PATH)) {
            throw new moodle1_convert_exception('file_invalid_path', $sourcepath);
        }

        $sourcefullpath = $this->basepath.'/'.$sourcepath;

        if (!is_readable($sourcefullpath)) {
            throw new moodle1_convert_exception('file_not_readable', $sourcefullpath);
        }

        // sanitize filepath
        if (empty($filepath)) {
            $filepath = '/';
        }
        if (substr($filepath, -1) !== '/') {
            $filepath .= '/';
        }
        $filepath = clean_param($filepath, PARAM_PATH);

        if (core_text::strlen($filepath) > 255) {
            throw new moodle1_convert_exception('file_path_longer_than_255_chars');
        }

        if (is_null($filename)) {
            $filename = basename($sourcefullpath);
        }

        $filename = clean_param($filename, PARAM_FILE);

        if ($filename === '') {
            throw new moodle1_convert_exception('unsupported_chars_in_filename');
        }

        if (is_null($timecreated)) {
            $timecreated = filectime($sourcefullpath);
        }

        if (is_null($timemodified)) {
            $timemodified = filemtime($sourcefullpath);
        }

        $filerecord = $this->make_file_record(array(
            'filepath'      => $filepath,
            'filename'      => $filename,
            'sortorder'     => $sortorder,
            'mimetype'      => mimeinfo('type', $sourcefullpath),
            'timecreated'   => $timecreated,
            'timemodified'  => $timemodified,
        ));

        list($filerecord['contenthash'], $filerecord['filesize'], $newfile) = $this->add_file_to_pool($sourcefullpath);
        $this->stash_file($filerecord);

        return $filerecord['id'];
    }

    /**
     * Migrates all files in the given directory
     *
     * @param string $rootpath path within the backup archive to the root directory containing the files {@example 'course_files'}
     * @param string $relpath relative path used during the recursion - do not provide when calling this!
     * @return array ids of the migrated files, empty array if the $rootpath not found
     */
    public function migrate_directory($rootpath, $relpath='/') {

        // Check the trailing slash in the $rootpath
        if (substr($rootpath, -1) === '/') {
            debugging('moodle1_file_manager::migrate_directory() expects $rootpath without the trailing slash', DEBUG_DEVELOPER);
            $rootpath = substr($rootpath, 0, strlen($rootpath) - 1);
        }

        if (!file_exists($this->basepath.'/'.$rootpath.$relpath)) {
            return array();
        }

        $fileids = array();

        // make the fake file record for the directory itself
        $filerecord = $this->make_file_record(array('filepath' => $relpath, 'filename' => '.'));
        $this->stash_file($filerecord);
        $fileids[] = $filerecord['id'];

        $items = new DirectoryIterator($this->basepath.'/'.$rootpath.$relpath);

        foreach ($items as $item) {

            if ($item->isDot()) {
                continue;
            }

            if ($item->isLink()) {
                throw new moodle1_convert_exception('unexpected_symlink');
            }

            if ($item->isFile()) {
                $fileids[] = $this->migrate_file(substr($item->getPathname(), strlen($this->basepath.'/')),
                    $relpath, $item->getFilename(), 0, $item->getCTime(), $item->getMTime());

            } else {
                $dirname = clean_param($item->getFilename(), PARAM_PATH);

                if ($dirname === '') {
                    throw new moodle1_convert_exception('unsupported_chars_in_filename');
                }

                // migrate subdirectories recursively
                $fileids = array_merge($fileids, $this->migrate_directory($rootpath, $relpath.$item->getFilename().'/'));
            }
        }

        return $fileids;
    }

    /**
     * Returns the list of all file ids migrated by this instance so far
     *
     * @return array of int
     */
    public function get_fileids() {
        return $this->fileids;
    }

    /**
     * Explicitly clear the list of file ids migrated by this instance so far
     */
    public function reset_fileids() {
        $this->fileids = array();
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

    /// internal implementation details ////////////////////////////////////////

    /**
     * Prepares a fake record from the files table
     *
     * @param array $fileinfo explicit file data
     * @return array
     */
    protected function make_file_record(array $fileinfo) {

        $defaultrecord = array(
            'contenthash'   => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',  // sha1 of an empty file
            'contextid'     => $this->contextid,
            'component'     => $this->component,
            'filearea'      => $this->filearea,
            'itemid'        => $this->itemid,
            'filepath'      => null,
            'filename'      => null,
            'filesize'      => 0,
            'userid'        => $this->userid,
            'mimetype'      => null,
            'status'        => 0,
            'timecreated'   => $now = time(),
            'timemodified'  => $now,
            'source'        => null,
            'author'        => null,
            'license'       => null,
            'sortorder'     => 0,
        );

        if (!array_key_exists('id', $fileinfo)) {
            $defaultrecord['id'] = $this->converter->get_nextid();
        }

        // override the default values with the explicit data provided and return
        return array_merge($defaultrecord, $fileinfo);
    }

    /**
     * Copies the given file to the pool directory
     *
     * Returns an array containing SHA1 hash of the file contents, the file size
     * and a flag indicating whether the file was actually added to the pool or whether
     * it was already there.
     *
     * @param string $pathname the full path to the file
     * @return array with keys (string)contenthash, (int)filesize, (bool)newfile
     */
    protected function add_file_to_pool($pathname) {

        if (!is_readable($pathname)) {
            throw new moodle1_convert_exception('file_not_readable');
        }

        $contenthash = sha1_file($pathname);
        $filesize    = filesize($pathname);
        $hashpath    = $this->converter->get_workdir_path().'/files/'.substr($contenthash, 0, 2);
        $hashfile    = "$hashpath/$contenthash";

        if (file_exists($hashfile)) {
            if (filesize($hashfile) !== $filesize) {
                // congratulations! you have found two files with different size and the same
                // content hash. or, something were wrong (which is more likely)
                throw new moodle1_convert_exception('same_hash_different_size');
            }
            $newfile = false;

        } else {
            check_dir_exists($hashpath);
            $newfile = true;

            if (!copy($pathname, $hashfile)) {
                throw new moodle1_convert_exception('unable_to_copy_file');
            }

            if (filesize($hashfile) !== $filesize) {
                throw new moodle1_convert_exception('filesize_different_after_copy');
            }
        }

        return array($contenthash, $filesize, $newfile);
    }

    /**
     * Stashes the file record into 'files' stash and adds the record id to list of migrated files
     *
     * @param array $filerecord
     */
    protected function stash_file(array $filerecord) {
        $this->converter->set_stash('files', $filerecord, $filerecord['id']);
        $this->fileids[] = $filerecord['id'];
    }
}


/**
 * Helper class that handles ids annotations for inforef.xml files
 */
class moodle1_inforef_manager {

    /** @var string the name of the annotator we serve to (like course, section, activity, block) */
    protected $annotator = null;

    /** @var int the id of the annotator if it can have multiple instances */
    protected $annotatorid = null;

    /** @var array the actual storage of references, currently implemented as a in-memory structure */
    private $refs = array();

    /**
     * Creates new instance of the manager for the given annotator
     *
     * The identification of the annotator we serve to may be important in the future
     * when we move the actual storage of the references from memory to a persistent storage.
     *
     * @param moodle1_converter $converter
     * @param string $name the name of the annotator (like course, section, activity, block)
     * @param int $id the id of the annotator if required
     */
    public function __construct(moodle1_converter $converter, $name, $id = 0) {
        $this->annotator   = $name;
        $this->annotatorid = $id;
    }

    /**
     * Adds a reference
     *
     * @param string $item the name of referenced item (like user, file, scale, outcome or grade_item)
     * @param int $id the value of the reference
     */
    public function add_ref($item, $id) {
        $this->validate_item($item);
        $this->refs[$item][$id] = true;
    }

    /**
     * Adds a bulk of references
     *
     * @param string $item the name of referenced item (like user, file, scale, outcome or grade_item)
     * @param array $ids the list of referenced ids
     */
    public function add_refs($item, array $ids) {
        $this->validate_item($item);
        foreach ($ids as $id) {
            $this->refs[$item][$id] = true;
        }
    }

    /**
     * Writes the current references using a given opened xml writer
     *
     * @param xml_writer $xmlwriter
     */
    public function write_refs(xml_writer $xmlwriter) {
        $xmlwriter->begin_tag('inforef');
        foreach ($this->refs as $item => $ids) {
            $xmlwriter->begin_tag($item.'ref');
            foreach (array_keys($ids) as $id) {
                $xmlwriter->full_tag($item, $id);
            }
            $xmlwriter->end_tag($item.'ref');
        }
        $xmlwriter->end_tag('inforef');
    }

    /**
     * Makes sure that the given name is a valid citizen of inforef.xml file
     *
     * @see backup_helper::get_inforef_itemnames()
     * @param string $item the name of reference (like user, file, scale, outcome or grade_item)
     * @throws coding_exception
     */
    protected function validate_item($item) {

        $allowed = array(
            'user'              => true,
            'grouping'          => true,
            'group'             => true,
            'role'              => true,
            'file'              => true,
            'scale'             => true,
            'outcome'           => true,
            'grade_item'        => true,
            'question_category' => true
        );

        if (!isset($allowed[$item])) {
            throw new coding_exception('Invalid inforef item type');
        }
    }
}
