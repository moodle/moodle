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
 * abstract block task that provides all the properties and common steps to be performed
 * when one block is being restored
 *
 * TODO: Finish phpdocs
 */
abstract class restore_block_task extends restore_task {

    protected $taskbasepath; // To store the basepath of this block

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $taskbasepath, $plan = null) {
        $this->taskbasepath = $taskbasepath;
        parent::__construct($name, $plan);
    }

    /**
     * Block tasks have their own directory to write files
     */
    public function get_taskbasepath() {
        return $this->taskbasepath;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // If we have decided not to backup blocks, prevent anything to be built
        if (!$this->get_setting_value('blocks')) {
            $this->built = true;
            return;
        }

        // TODO: See backup. If "child" of activity task and it has been excluded, nothing to do


        // TODO: Link all the activity steps here

        // At the end, mark it as built
        $this->built = true;
    }

// Protected API starts here

    /**
     * Define the common setting that any backup block will have
     */
    protected function define_settings() {

        // Nothing to add, blocks doesn't have common settings (for now)

        // End of common activity settings, let's add the particular ones
        $this->define_my_settings();
    }

    /**
     * Define (add) particular settings that each block can have
     */
    abstract protected function define_my_settings();

    /**
     * Define (add) particular steps that each block can have
     */
    abstract protected function define_my_steps();

    /**
     * Define one array() of configdata attributes
     * that need to be decoded
     */
    abstract public function get_configdata_encoded_attributes();

    /**
     * Code the transformations to perform by the block in
     * order to get encoded transformed back to working links
     */
    abstract static public function decode_content_links($content);
}
