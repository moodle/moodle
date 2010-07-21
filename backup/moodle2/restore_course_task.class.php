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
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * course task that provides all the properties and common steps to be performed
 * when one course is being restored
 *
 * TODO: Finish phpdocs
 */
class restore_course_task extends restore_task {

    protected $info; // info related to course gathered from backup file

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {
        $this->info = $info;
        parent::__construct($name, $plan);
    }

    /**
     * Course tasks have their own directory to read files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/course';
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // TODO: Link all the course steps here
        // Executed conditionally if restoring to new course or deleting or if overwrite_conf setting is enabled
        if ($this->get_target() == backup::TARGET_NEW_COURSE || $this->get_setting_value('overwrite_conf') == true) {
            $this->add_step(new restore_course_structure_step('course_info', 'course.xml'));
        }

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Code the transformations to perform in the course in
     * order to get encoded transformed back to working links
     */
    static public function decode_content_links($content) {

        // TODO: Decode COURSEVIEWBYID

        return $content;
    }

// Protected API starts here

    /**
     * Define the common setting that any restore course will have
     */
    protected function define_settings() {

        // Define overwrite_conf to decide if course configuration will be restored over existing one
        $overwrite = new restore_course_overwrite_conf_setting('overwrite_conf', base_setting::IS_BOOLEAN, false);
        $overwrite->set_ui(new backup_setting_ui_select($overwrite, $overwrite->get_name(), array(1=>get_string('yes'), 0=>get_string('no'))));
        $this->add_setting($overwrite);

    }
}
