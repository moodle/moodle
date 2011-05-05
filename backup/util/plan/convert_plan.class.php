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
 * Convert plan
 */
class convert_plan extends base_plan implements loggable {
    /**
     * @var plan_converter
     */
    protected $converter;

    public function __construct(plan_converter $converter) {
        $this->converter = $converter;
        parent::__construct('convert_plan');
    }

    /**
     * This function will be responsible for handling the params, and to call
     * to the corresponding logger->process() once all modifications in params
     * have been performed
     */
    public function log($message, $level, $a = null, $depth = null, $display = false) {
        // TODO: Implement log() method.
    }

    public function get_basepath() {
        return $this->converter->get_workdir_path();
    }

    /**
     * @return plan_converter
     */
    public function get_converter() {
        return $this->converter;
    }

    public function get_converterid() {
        return $this->converter->get_id();
    }

    /**
     * Function responsible for building the tasks of any plan
     * with their corresponding settings
     * (must set the $built property to true)
     */
    public function build() {
        // This seems circular for no real reason....
        $this->converter->build_plan();
        $this->built = true;
    }

    /**
     * Execute the after_restore methods of all the executed tasks in the plan
     */
    public function execute_after_convert() {
        // Simply iterate over each task in the plan and delegate to them the execution
        foreach ($this->tasks as $task) {
            $task->execute_after_convert();
        }
    }
}
