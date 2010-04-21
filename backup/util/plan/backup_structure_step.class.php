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
        parent::__construct($name, $task);
    }

    public function execute() {

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
    }

// Protected API starts here

    /**
     * This function simply marks one param to be considered as straight sql
     * param, so it won't be searched in the structure tree nor converted at
     * all. Useful for better integration of definition of sources in structure
     * and DB stuff.
     */
    protected function is_sqlparam($value) {
        return array('sqlparam' => $value);
    }


    /**
     * Function that will return the structure to be processed by this backup_step.
     * Must return one backup_nested_element
     */
    abstract protected function define_structure();
}
