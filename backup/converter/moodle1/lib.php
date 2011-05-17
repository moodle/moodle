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

    /** @var string the current module being processed */
    protected $currentmod = '';

    /** @var string the current block being processed */
    protected $currentblock = '';

    /** @var string path currently locking processing of children */
    protected $pathlock;

    /**
     * Instructs the dispatcher to ignore all children below path processor returning it
     */
    const SKIP_ALL_CHILDREN = -991399;

    /**
     * Detects the Moodle 1.9 format of the backup directory
     *
     * @param string $tempdir the name of the backup directory
     * @return null|string backup::FORMAT_MOODLE1 if the Moodle 1.9 is detected, null otherwise
     */
    public static function detect_format($tempdir) {
        global $CFG;

        $filepath = $CFG->dataroot . '/temp/backup/' . $tempdir . '/moodle.xml';
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

        // good boy, prepare XML parser and processor
        $this->xmlparser = new progressive_parser();
        $this->xmlparser->set_file($this->get_tempdir_path() . '/moodle.xml');
        $this->xmlprocessor = new moodle1_parser_processor($this);
        $this->xmlparser->set_processor($this->xmlprocessor);

        // make sure that MOD and BLOCK paths are visited
        $this->xmlprocessor->add_path('/MOODLE_BACKUP/COURSE/MODULES/MOD');
        $this->xmlprocessor->add_path('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK');

        // register the conversion handlers
        foreach (moodle1_handlers_factory::get_handlers($this) as $handler) {
            $this->register_handler($handler, $handler->get_paths());
        }
    }

    /**
     * Converts the contents of the tempdir into the target format in the workdir
     */
    protected function execute() {
        $this->create_stash_storage();
        $this->xmlparser->process();
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
            $path = str_replace('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentmod, $path);
        }

        if ($path !== $data['path']) {
            if (!array_key_exists($path, $this->pathelements)) {
                // no handler registered for the transformed MOD or BLOCK path
                // todo add this event to the convert log instead of debugging
                //debugging('No handler registered for the path ' . $path);
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
            throw new convert_exception('missing_processing_object', $object);
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
            $returned     = $object->$method($data['tags'], $rawdatatags);
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
            $element->set_data($returned);

        // use just the cooked parsed data otherwise
        } else {
            $element->set_data($data);
        }
    }

    /**
     * Executes operations required at the start of a watched path
     *
     * Note that this is called before the MOD and BLOCK paths are expanded
     * so the current plugin is not known yet. Also note that this is
     * triggered before the previous path is actually dispatched.
     *
     * @param string $path in the original file
     */
    public function path_start_reached($path) {

        if (empty($this->pathelements[$path])) {
            return;
        }

        $element = $this->pathelements[$path];
        $pobject = $element->get_processing_object();
        $method  = 'on_' . $element->get_name() . '_start';

        if (method_exists($pobject, $method)) {
            $pobject->$method();
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
            $path = str_replace('/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/' . $this->currentmod, $path);
        }

        if (empty($this->pathelements[$path])) {
            return;
        }

        $element = $this->pathelements[$path];
        $pobject = $element->get_processing_object();
        $data    = $element->get_data();
        $method  = 'on_' . $element->get_name() . '_end';

        if (method_exists($pobject, $method)) {
            $pobject->$method($data['tags']);
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
            throw new moodle1_convert_empty_storage_exception('required_not_stashed_data');
        } else {
            return $record->info;
        }
    }

    /**
     * Generates an artificial context id
     *
     * Moodle 1.9 backups do not contain any context information. But we need them
     * in Moodle 2.x format so here we generate fictive context id for every given
     * context level + instance combo.
     *
     * @see get_context_instance()
     * @param int $level the context level, like CONTEXT_COURSE or CONTEXT_MODULE
     * @param int $instance the instance id, for example $course->id for courses or $cm->id for activity modules
     * @return int the context id
     */
    public function get_contextid($level, $instance) {
        static $autoincrement = 0;

        $stashname = 'context' . $level;

        try {
            // try the previously stashed id
            return $this->get_stash($stashname, $instance);

        } catch (moodle1_convert_empty_storage_exception $e) {
            // this context level + instance is required for the first time
            $this->set_stash($stashname, ++$autoincrement, $instance);
            return $autoincrement;
        }
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
 * XML parser processor
 */
class moodle1_parser_processor extends grouped_parser_processor {

    /** @var moodle1_converter */
    protected $converter;

    public function __construct(moodle1_converter $converter) {
        $this->converter = $converter;
        parent::__construct();
    }

    /**
     * Provide NULL and legacy file.php uses decoding
     */
    public function process_cdata($cdata) {
        global $CFG;

        if ($cdata === '$@NULL@$') {  // Some cases we know we can skip complete processing
            return null;
        } else if ($cdata === '') {
            return '';
        } else if (is_numeric($cdata)) {
            return $cdata;
        } else if (strlen($cdata) < 32) { // Impossible to have one link in 32cc
            return $cdata;                // (http://10.0.0.1/file.php/1/1.jpg, http://10.0.0.1/mod/url/view.php?id=)
        } else if (strpos($cdata, '$@FILEPHP@$') === false) { // No $@FILEPHP@$, nothing to convert
            return $cdata;
        }
        // Decode file.php calls
        $search = array ("$@FILEPHP@$");
        $replace = array(get_file_url($this->courseid));
        $result = str_replace($search, $replace, $cdata);
        // Now $@SLASH@$ and $@FORCEDOWNLOAD@$ MDL-18799
        $search = array('$@SLASH@$', '$@FORCEDOWNLOAD@$');
        if ($CFG->slasharguments) {
            $replace = array('/', '?forcedownload=1');
        } else {
            $replace = array('%2F', '&amp;forcedownload=1');
        }
        return str_replace($search, $replace, $result);
    }

    /**
     * Override this method so we'll be able to skip
     * dispatching some well-known chunks, like the
     * ones being 100% part of subplugins stuff. Useful
     * for allowing development without having all the
     * possible restore subplugins defined
     */
    protected function postprocess_chunk($data) {

        // Iterate over all the data tags, if any of them is
        // not 'subplugin_XXXX' or has value, then it's a valid chunk,
        // pass it to standard (parent) processing of chunks.
        foreach ($data['tags'] as $key => $value) {
            if (trim($value) !== '' || strpos($key, 'subplugin_') !== 0) {
                parent::postprocess_chunk($data);
                return;
            }
        }
        // Arrived here, all the tags correspond to sublplugins and are empty,
        // skip the chunk, and debug_developer notice
        $this->chunks--; // not counted
        debugging('Missing support on restore for ' . clean_param($data['path'], PARAM_PATH) .
                  ' subplugin (' . implode(', ', array_keys($data['tags'])) .')', DEBUG_DEVELOPER);
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

    /** @var mixed last data read for this element or returned data by processing method */
    protected $data = null;

    /** @var array of deprecated fields that are dropped */
    protected $dropfields = array();

    /** @var array of fields renaming */
    protected $renamefields = array();

    /** @var array of new fields to add and their initial values */
    protected $newfields = array();

    /**
     * Constructor
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

        // set the default processing method name
        $this->set_processing_method('process_' . $name);

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
     * Sets the element data
     *
     * @param mixed
     */
    public function set_data($data) {
        $this->data = $data;
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
     * @return mixed the element data
     */
    public function get_data() {
        return $this->data;
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
     * The processing object must be an object providing the element's processing method.
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
            throw new convert_path_exception('convert_path_no_object', $pobject);
        }
        if (!method_exists($pobject, $this->get_processing_method())) {
            throw new convert_path_exception('convert_path_missingmethod', $this->get_processing_method());
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
