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
 * @package moodlecore
 * @subpackage backup-plan
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining the needed stuff to backup one @backup_structure
 *
 * TODO: Finish phpdocs
 */
abstract class backup_structure_step extends backup_step {

    protected $filename; // Name of the file to be generated
    protected $contenttransformer; // xml content transformer being used
                                   // (need it here, apart from xml_writer,
                                   // thanks to serialized data to process -
                                   // say thanks to blocks!)

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $filename, $task = null) {
        if (!is_null($task) && !($task instanceof backup_task)) {
            throw new backup_step_exception('wrong_backup_task_specified');
        }
        $this->filename = $filename;
        $this->contenttransformer = null;
        parent::__construct($name, $task);
    }

    public function execute() {

        if (!$this->execute_condition()) { // Check any condition to execute this
            return;
        }

        $fullpath = $this->task->get_taskbasepath();

        // We MUST have one fullpath here, else, error
        if (empty($fullpath)) {
            throw new backup_step_exception('backup_structure_step_undefined_fullpath');
        }

        // Append the filename to the fullpath
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;

        // Create output, transformer, writer, processor
        $xo = new file_xml_output($fullpath);
        $xt = null;
        if (class_exists('backup_xml_transformer')) {
            $xt = new backup_xml_transformer($this->get_courseid());
            $this->contenttransformer = $xt; // Save the reference to the transformer
                                             // as far as we are going to need it out
                                             // from xml_writer (blame serialized data!)
        }
        $xw = new xml_writer($xo, $xt);
        $pr = new backup_structure_processor($xw);

        // Set processor variables from settings
        foreach ($this->get_settings() as $setting) {
            $pr->set_var($setting->get_name(), $setting->get_value());
        }
        // Add backupid as one more var for processor
        $pr->set_var(backup::VAR_BACKUPID, $this->get_backupid());

        // Get structure definition
        $structure = $this->define_structure();
        if (! $structure instanceof backup_nested_element) {
            throw new backup_step_exception('backup_structure_step_wrong_structure');
        }

        // Start writer
        $xw->start();

        // Process structure definition
        $structure->process($pr);

        // Close everything
        $xw->stop();

        // Destroy the structure. It helps PHP 5.2 memory a lot!
        $structure->destroy();
    }

    /**
     * As far as backup structure steps are implementing backup_plugin stuff, they need to
     * have the parent task available for wrapping purposes (get course/context....)
     */
    public function get_task() {
        return $this->task;
    }

// Protected API starts here

    /**
     * Add plugin structure to any element in the structure backup tree
     *
     * @param string $plugintype type of plugin as defined by get_plugin_types()
     * @param backup_nested_element $element element in the structure backup tree that
     *                                       we are going to add plugin information to
     * @param bool $multiple to define if multiple plugins can produce information
     *                       for each instance of $element (true) or no (false)
     */
    protected function add_plugin_structure($plugintype, $element, $multiple) {

        global $CFG;

        // Check the requested plugintype is a valid one
        if (!array_key_exists($plugintype, get_plugin_types($plugintype))) {
             throw new backup_step_exception('incorrect_plugin_type', $plugintype);
        }

        // Arrived here, plugin is correct, let's create the optigroup
        $optigroupname = $plugintype . '_' . $element->get_name() . '_plugin';
        $optigroup = new backup_optigroup($optigroupname, null, $multiple);
        $element->add_child($optigroup); // Add optigroup to stay connected since beginning

        // Get all the optigroup_elements, looking across all the plugin dirs
        $pluginsdirs = get_plugin_list($plugintype);
        foreach ($pluginsdirs as $name => $plugindir) {
            $classname = 'backup_' . $plugintype . '_' . $name . '_plugin';
            $backupfile = $plugindir . '/backup/moodle2/' . $classname . '.class.php';
            if (file_exists($backupfile)) {
                require_once($backupfile);
                $backupplugin = new $classname($plugintype, $name, $optigroup, $this);
                // Add plugin returned structure to optigroup
                $backupplugin->define_plugin_structure($element->get_name());
            }
        }
    }

    /**
     * To conditionally decide if one step will be executed or no
     *
     * For steps needing to be executed conditionally, based in dynamic
     * conditions (at execution time vs at declaration time) you must
     * override this function. It will return true if the step must be
     * executed and false if not
     */
    protected function execute_condition() {
        return true;
    }

    /**
     * Function that will return the structure to be processed by this backup_step.
     * Must return one backup_nested_element
     */
    abstract protected function define_structure();
}
