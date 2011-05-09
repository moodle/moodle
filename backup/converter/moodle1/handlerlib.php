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
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');

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

        $handlers = array_merge($handlers, self::get_plugin_handlers('mod', $converter));
        $handlers = array_merge($handlers, self::get_plugin_handlers('block', $converter));

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
    public static function get_plugin_handlers($type, moodle1_converter $converter) {
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
 * General backup conversion handler
 */
abstract class moodle1_handler {

    /** @var moodle1_converter */
    protected $converter;

    /** @var xml_writer */
    protected $xmlwriter;

    /**
     * @param moodle1_converter $converter the converter that requires us
     */
    public function __construct(moodle1_converter $converter) {

        $this->converter = $converter;
    }

    /**
     * Opens the XML writer - after calling, one is free to use $xmlwriter
     *
     * @return void
     */
    public function open_xml_writer() {

        if (is_null($this->get_xml_filename())) {
            throw new convert_exception('handler_not_expected_to_write_xml');
        }

        if (!$this->xmlwriter instanceof xml_writer) {
            $fullpath  = $this->converter->get_workdir_path() . '/' . $this->get_xml_filename();
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
            unset($this->xmlwriter);
            $this->xmlwriter = null;
        }
    }

    /**
     * @return string the file name of the target XML file to write into
     */
    abstract protected function get_xml_filename();
}


/**
 * Shared base class for activity modules and blocks handlers
 */
abstract class moodle1_plugin_handler extends moodle1_handler {

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
abstract class moodle1_block_handler extends moodle1_handler {

}


/**
 * Process the root element of the backup file
 */
class moodle1_root_handler extends moodle1_handler {

    public function get_paths() {
        return array(new convert_path('root_element', '/MOODLE_BACKUP'));
    }

    public function process_root_element($data) {
        // no data available - nothing to do
    }

    /**
     * This is executed at the very start of the moodle.xml parsing
     */
    public function on_root_element_start() {

        // create ids temp table
        backup_controller_dbops::create_backup_ids_temp_table($this->converter->get_id());
    }

    /**
     * This is executed at the end of the moodle.xml parsing
     */
    public function on_root_element_end() {

        // drop the ids temp table
        backup_controller_dbops::drop_backup_ids_temp_table($this->converter->get_id());
    }

    /**
     * This handler does not actually produces any output
     */
    protected function get_xml_filename() {
        return null;
    }
}
