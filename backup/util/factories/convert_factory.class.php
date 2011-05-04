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
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Factory class to create new instances of backup converters
 */
abstract class convert_factory {

    /**
     * Instantinates the given converter operating on a given directory
     *
     * @throws coding_exception
     * @param  $name The converter name
     * @param  $tempdir The temp directory to operate on
     * @return base_converter|plan_converter
     */
    public static function converter($name, $tempdir) {
        global $CFG;

        $name = clean_param($name, PARAM_SAFEDIR);

        $classfile = "$CFG->dirroot/backup/converter/$name/converter.class.php";
        $classname = "{$name}_converter";

        if (!file_exists($classfile)) {
            throw new coding_exception("Converter factory error: class file not found $classfile");
        }
        require_once($classfile);

        if (!class_exists($classname)) {
            throw new coding_exception("Converter factory error: class not found $classname");
        }
        return new $classname($tempdir);
    }
    
    /**
     * Instantiates a list of all installed converters operating on a given directory
     *
     * @param string $tempdir The temp directory to operate on
     * @return array
     */
    public static function converters($tempdir) {
        global $CFG;

        $converters = array();
        $plugins    = get_list_of_plugins('backup/converter');
        foreach ($plugins as $name) {
            $converters[$name] = self::converter($name, $tempdir);
        }
        return $converters;
    }

    /**
     * Runs through all plugins of a specific type and instantiates their task class
     *
     * @throws coding_exception
     * @param string $type The plugin type
     * @param string $format The convert format
     * @param string $extra Extra naming structure
     * @return array
     */
    public static function get_plugin_tasks($type, $format, $extra = NULL) {
        global $CFG; // REQUIRED by task file includes

        if (is_null($extra)) {
            $extra = $type;
        }
        $tasks   = array();
        $plugins = get_plugin_list($type);
        foreach ($plugins as $name => $dir) {
            $taskfile  = "$dir/backup/$format/convert_{$name}_{$extra}_task.class.php";
            $taskclass = "{$format}_{$name}_{$extra}_task";
            if (!file_exists($taskfile)) {
                continue;
            }
            require_once($taskfile);

            if (!class_exists($taskclass)) {
                throw new coding_exception("The class name should be $taskclass in $taskfile");
            }
            $tasks[] = new $taskclass("{$type}_$name");
        }
        return $tasks;
    }

    /**
     * Adds all of the plugin tasks to the given converter's plan
     *
     * @param plan_converter $converter The converter to add the plugin tasks to
     * @param string $type The plugin type
     * @param string $extra Extra naming structure
     * @return void
     */
    public static function build_plugin_tasks(plan_converter $converter, $type, $extra = NULL) {
        $tasks = self::get_plugin_tasks($type, $converter->get_name(), $extra);
        foreach ($tasks as $task) {
            $converter->get_plan()->add_task($task);
        }
    }
}
