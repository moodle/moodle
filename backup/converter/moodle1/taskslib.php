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

require_once($CFG->dirroot . '/backup/util/includes/convert_includes.php');

class moodle1_root_task extends convert_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->add_step(new convert_create_and_clean_temp_stuff('create_and_clean_temp_stuff'));

        // At the end, mark it as built
        $this->built = true;
    }

}

/**
 * @todo Not used at the moment
 */
class moodle1_final_task extends convert_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->add_step(new convert_drop_and_clean_temp_stuff('drop_and_clean_temp_stuff'));

        // At the end, mark it as built
        $this->built = true;
    }
}

class moodle1_course_task extends convert_task {
    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        $this->add_step(new moodle1_course_structure_step('course'));
        $this->add_step(new moodle1_section_structure_step('course_section'));
        $this->add_step(new moodle1_block_structure_step('course_blocks'));
        $this->add_step(new moodle1_info_structure_step('info'));

        // At the end, mark it as built
        $this->built = true;
    }
}

abstract class moodle1_plugin_task extends convert_task {
    /**
     * Plugin specific steps
     */
    abstract protected function define_my_steps();
}

abstract class moodle1_activity_task extends moodle1_plugin_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->define_my_steps();

        // @todo Risky?
        list($plugin, $name) = explode('_', $this->name);

        $this->add_step(new moodle1_module_structure_step("{$this->name}_module", $name));
        $this->built = true;
    }
}

abstract class moodle1_block_task extends moodle1_plugin_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->define_my_steps();
        $this->built = true;
    }
}
